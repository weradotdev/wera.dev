<x-filament-panels::page>
    {{-- Full-bleed meeting room --}}
    <div
        class="-mx-6 -mb-6 flex overflow-hidden bg-[#202124]"
        style="height: calc(100vh - 8.5rem); min-height: 480px;"
        x-data="{
            meetingId: @js($this->meeting?->getKey()),
            currentUserId: @js((int) auth()->id()),
            currentUserName: @js(auth()->user()?->first_name ?? '?'),
            meetingTitle: @js($this->meeting?->title ?? 'Meeting'),
            attendeesList: @js($this->attendees()->map(fn ($u) => ['userId' => $u->id, 'name' => trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')) ?: $u->email])->values()->all()),

            localStream: null,
            micOn: true,
            camOn: true,
            peers: {},
            participants: [],
            showPanel: false,
            panelTab: 'people',
            elapsed: 0,
            _timer: null,

            get elapsedFormatted() {
                const h = Math.floor(this.elapsed / 3600);
                const m = Math.floor((this.elapsed % 3600) / 60);
                const s = this.elapsed % 60;
                if (h > 0) return h + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            },

            get participantCount() {
                return this.participants.length + 1;
            },

            async init() {
                this._timer = setInterval(() => this.elapsed++, 1000);
                await this.startCamera();
                await this.initRoom();
            },

            async initRoom() {
                if (!this.meetingId || !window.Echo) return;

                window.Echo.private('meetings.' + this.meetingId)
                    .listen('.meeting.signal', async (event) => {
                        if (!event || event.from_user_id === this.currentUserId) return;
                        if (event.to_user_id && event.to_user_id !== this.currentUserId) return;

                        if (event.type === 'join') { await this.callUser(event.from_user_id); return; }
                        if (event.type === 'leave') { this.removePeer(event.from_user_id); return; }

                        await this.handleSignal(event.from_user_id, event.type, event.payload || {});
                    });

                await $wire.sendSignal('join', {});
            },

            async startCamera() {
                if (this.localStream) return;
                try {
                    this.localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                    await this.$nextTick();
                    if (this.$refs.localVideo) this.$refs.localVideo.srcObject = this.localStream;
                } catch (e) {
                    console.warn('Camera/mic access denied', e);
                }
            },

            toggleMic() {
                if (!this.localStream) return;
                const track = this.localStream.getAudioTracks()[0];
                if (track) { this.micOn = !this.micOn; track.enabled = this.micOn; }
            },

            toggleCamera() {
                if (!this.localStream) return;
                const track = this.localStream.getVideoTracks()[0];
                if (track) { this.camOn = !this.camOn; track.enabled = this.camOn; }
            },

            createPeer(targetUserId) {
                if (this.peers[targetUserId]) return this.peers[targetUserId];

                const peer = new RTCPeerConnection({ iceServers: [{ urls: 'stun:stun.l.google.com:19302' }] });

                if (this.localStream) {
                    this.localStream.getTracks().forEach(t => peer.addTrack(t, this.localStream));
                }

                peer.onicecandidate = (e) => {
                    if (!e.candidate) return;
                    $wire.sendSignal('ice-candidate', { candidate: e.candidate }, Number(targetUserId));
                };

                peer.ontrack = (e) => {
                    const a = this.attendeesList.find(x => x.userId === targetUserId);
                    this.addParticipant(targetUserId, e.streams[0], a ? a.name : 'Participant');
                };

                peer.onconnectionstatechange = () => {
                    if (['disconnected', 'failed', 'closed'].includes(peer.connectionState)) {
                        this.removePeer(targetUserId);
                    }
                };

                this.peers[targetUserId] = peer;
                return peer;
            },

            addParticipant(userId, stream, name) {
                const idx = this.participants.findIndex(p => p.userId === userId);
                if (idx >= 0) {
                    this.participants[idx].stream = stream;
                } else {
                    this.participants.push({ userId, name, stream });
                }
                this.$nextTick(() => {
                    const el = document.getElementById('vp-' + userId);
                    if (el) el.srcObject = stream;
                });
            },

            removePeer(userId) {
                if (this.peers[userId]) { this.peers[userId].close(); delete this.peers[userId]; }
                this.participants = this.participants.filter(p => p.userId !== userId);
            },

            async callUser(targetUserId) {
                const peer = this.createPeer(targetUserId);
                const offer = await peer.createOffer();
                await peer.setLocalDescription(offer);
                await $wire.sendSignal('offer', { sdp: offer }, Number(targetUserId));
            },

            async callAll() {
                const ids = @js($this->attendees()->pluck('id')->values()->all());
                for (const id of ids) {
                    if (id !== this.currentUserId) await this.callUser(id);
                }
            },

            async handleSignal(fromUserId, type, payload) {
                const peer = this.createPeer(fromUserId);

                if (type === 'offer' && payload.sdp) {
                    await peer.setRemoteDescription(new RTCSessionDescription(payload.sdp));
                    const answer = await peer.createAnswer();
                    await peer.setLocalDescription(answer);
                    await $wire.sendSignal('answer', { sdp: answer }, Number(fromUserId));
                }
                if (type === 'answer' && payload.sdp) {
                    await peer.setRemoteDescription(new RTCSessionDescription(payload.sdp));
                }
                if (type === 'ice-candidate' && payload.candidate) {
                    await peer.addIceCandidate(new RTCIceCandidate(payload.candidate));
                }
            },

            async leaveRoom() {
                clearInterval(this._timer);
                await $wire.sendSignal('leave', {});
                await $wire.markLeft();
                Object.keys(this.peers).forEach(id => this.removePeer(id));
                if (this.localStream) {
                    this.localStream.getTracks().forEach(t => t.stop());
                    this.localStream = null;
                }
                window.history.back();
            }
        }"
        x-init="init()"
        x-cloak
    >
        {{-- ── MAIN COLUMN ───────────────────────────────────────── --}}
        <div class="flex flex-col flex-1 min-w-0">

            {{-- TOP BAR --}}
            <div class="flex items-center justify-between px-5 py-3 shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="text-white font-medium text-sm truncate" x-text="meetingTitle"></span>
                    <span class="text-[#9aa0a6] text-xs font-mono tabular-nums shrink-0" x-text="elapsedFormatted"></span>
                </div>
                <span class="text-[#9aa0a6] text-xs shrink-0 ml-4"
                      x-text="participantCount + (participantCount === 1 ? ' participant' : ' participants')"></span>
            </div>

            {{-- VIDEO GRID --}}
            <div class="relative flex-1 flex items-center justify-center p-3 overflow-hidden">

                {{-- Remote participant tiles --}}
                <div
                    class="w-full h-full flex flex-wrap gap-2 items-center justify-center content-center"
                    :class="{ 'hidden': participants.length === 0 }"
                >
                    <template x-for="p in participants" :key="p.userId">
                        <div
                            class="relative rounded-2xl overflow-hidden bg-[#3c4043] flex items-center justify-center"
                            :style="participants.length === 1
                                ? 'width: min(72vh * 1.78, 100%); aspect-ratio: 16/9;'
                                : 'flex: 1 1 280px; max-width: 560px; aspect-ratio: 16/9;'"
                        >
                            {{-- Video --}}
                            <video
                                :id="'vp-' + p.userId"
                                autoplay playsinline
                                class="absolute inset-0 w-full h-full object-cover"
                            ></video>

                            {{-- Avatar when no video --}}
                            <div class="absolute inset-0 flex items-center justify-center bg-[#3c4043]">
                                <div
                                    class="w-20 h-20 rounded-full bg-[#5f6368] flex items-center justify-center text-white text-3xl font-semibold"
                                    x-text="p.name.charAt(0).toUpperCase()"
                                ></div>
                            </div>

                            {{-- Name badge --}}
                            <div class="absolute bottom-3 left-3 flex items-center gap-1.5 bg-black/60 backdrop-blur-sm text-white text-xs px-2.5 py-1 rounded-lg">
                                <span x-text="p.name"></span>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Empty / waiting state --}}
                <div
                    x-show="participants.length === 0"
                    class="flex flex-col items-center gap-5 text-center"
                >
                    <div class="w-28 h-28 rounded-full bg-[#3c4043] flex items-center justify-center">
                        <svg class="w-14 h-14 text-[#9aa0a6]" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-medium">Waiting for others to join</p>
                        <p class="text-[#9aa0a6] text-sm mt-1">Others will connect when they open the meeting link</p>
                    </div>
                    <button
                        @click="callAll()"
                        class="flex items-center gap-2 bg-[#8ab4f8] hover:bg-[#aecbfa] text-[#202124] font-medium px-5 py-2.5 rounded-full text-sm transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 6v.75z"/>
                        </svg>
                        Call attendees
                    </button>
                </div>

                {{-- Self-view (floating tile, bottom-right) --}}
                <div
                    class="absolute bottom-4 right-4 rounded-xl overflow-hidden bg-[#3c4043] shadow-2xl ring-1 ring-white/10 cursor-pointer"
                    style="width: 168px; aspect-ratio: 16/9;"
                    title="Your camera"
                >
                    <video
                        x-ref="localVideo"
                        autoplay playsinline muted
                        class="absolute inset-0 w-full h-full object-cover"
                        :class="{ 'opacity-0': !camOn }"
                    ></video>
                    {{-- Avatar when cam off --}}
                    <div
                        class="absolute inset-0 flex items-center justify-center bg-[#3c4043]"
                        :class="camOn ? 'opacity-0 pointer-events-none' : 'opacity-100'"
                    >
                        <div
                            class="w-10 h-10 rounded-full bg-[#5f6368] flex items-center justify-center text-white font-semibold"
                            x-text="currentUserName.charAt(0).toUpperCase()"
                        ></div>
                    </div>
                    <div class="absolute bottom-1.5 left-2 text-white text-[10px] font-medium">You</div>
                    <div class="absolute bottom-1.5 right-2" x-show="!micOn">
                        <svg class="w-3 h-3 text-red-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3.53 2.47a.75.75 0 00-1.06 1.06l18 18a.75.75 0 101.06-1.06l-18-18zM9 4.5a3 3 0 015.47-1.68L9.17 7.65A3.001 3.001 0 019 6.75V4.5zm-3 7.5a6 6 0 001.42 3.9L5.56 13.56A7.46 7.46 0 016 12zm6 6a6 6 0 01-5.97-5.47l-1.5-1.5A7.5 7.5 0 0012 19.5a7.5 7.5 0 007.5-7.5h-1.5A6 6 0 0112 18z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- BOTTOM CONTROL BAR --}}
            <div class="shrink-0 flex items-center justify-between px-6 py-4 gap-4">

                {{-- Left: title + timer --}}
                <div class="w-44 hidden lg:block min-w-0">
                    <p class="text-white text-xs font-medium truncate" x-text="meetingTitle"></p>
                    <p class="text-[#9aa0a6] text-xs font-mono tabular-nums" x-text="elapsedFormatted"></p>
                </div>

                {{-- Center: controls --}}
                <div class="flex items-center gap-3 mx-auto">

                    {{-- Microphone --}}
                    <button
                        @click="toggleMic()"
                        :class="micOn
                            ? 'bg-[#3c4043] hover:bg-[#5f6368] text-white'
                            : 'bg-white hover:bg-gray-200 text-[#202124]'"
                        class="relative w-14 h-14 rounded-full flex items-center justify-center transition-colors"
                        :title="micOn ? 'Turn off mic' : 'Turn on mic'"
                    >
                        {{-- Mic on --}}
                        <svg x-show="micOn" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z"/>
                        </svg>
                        {{-- Mic off --}}
                        <svg x-show="!micOn" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 19L5 5m7 14.75a6 6 0 006-6v-1.5M12 18.75a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3v-3m3-3a3 3 0 013 3v2.25"/>
                        </svg>
                    </button>

                    {{-- Camera --}}
                    <button
                        @click="toggleCamera()"
                        :class="camOn
                            ? 'bg-[#3c4043] hover:bg-[#5f6368] text-white'
                            : 'bg-white hover:bg-gray-200 text-[#202124]'"
                        class="w-14 h-14 rounded-full flex items-center justify-center transition-colors"
                        :title="camOn ? 'Turn off camera' : 'Turn on camera'"
                    >
                        {{-- Cam on --}}
                        <svg x-show="camOn" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z"/>
                        </svg>
                        {{-- Cam off --}}
                        <svg x-show="!camOn" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25ZM2.25 3l18 18"/>
                        </svg>
                    </button>

                    {{-- Leave / End call --}}
                    <button
                        @click="leaveRoom()"
                        class="bg-[#ea4335] hover:bg-[#d93025] text-white h-14 px-7 rounded-full flex items-center gap-2 transition-colors font-medium text-sm"
                        title="Leave call"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 3.75 18 6m0 0 2.25 2.25M18 6l2.25-2.25M18 6l-2.25 2.25m1.5 13.5c-8.284 0-15-6.716-15-15a.75.75 0 01.75-.75h4.5a.75.75 0 01.75.688 10.5 10.5 0 00.533 2.917c.103.376-.35.818-.652.937l-1.293.971c-.248.186-.398.502-.25.797a15.75 15.75 0 007.143 7.144c.295.147.611-.003.798-.251l.97-1.293c.12-.302.562-.755.938-.652a10.5 10.5 0 002.917.533.75.75 0 01.688.75v4.5a.75.75 0 01-.75.75A15.75 15.75 0 011.5 18"/>
                        </svg>
                        Leave
                    </button>
                </div>

                {{-- Right: panel toggles --}}
                <div class="w-44 flex justify-end items-center gap-2">
                    {{-- People --}}
                    <button
                        @click="showPanel = (panelTab !== 'people' || !showPanel); panelTab = 'people';"
                        :class="showPanel && panelTab === 'people'
                            ? 'bg-[#8ab4f8] text-[#202124]'
                            : 'bg-[#3c4043] hover:bg-[#5f6368] text-white'"
                        class="w-12 h-12 rounded-full flex items-center justify-center transition-colors relative"
                        title="Participants"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                        </svg>
                        <span
                            class="absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full bg-[#8ab4f8] text-[#202124] text-[9px] font-bold flex items-center justify-center"
                            x-text="participantCount"
                        ></span>
                    </button>

                    @if($this->canManageMeeting())
                        {{-- Invite --}}
                        <button
                            @click="showPanel = (panelTab !== 'invite' || !showPanel); panelTab = 'invite';"
                            :class="showPanel && panelTab === 'invite'
                                ? 'bg-[#8ab4f8] text-[#202124]'
                                : 'bg-[#3c4043] hover:bg-[#5f6368] text-white'"
                            class="w-12 h-12 rounded-full flex items-center justify-center transition-colors"
                            title="Invite people"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.765z"/>
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── SIDE PANEL ─────────────────────────────────────────── --}}
        <div
            x-show="showPanel"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-x-8"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-8"
            class="w-80 shrink-0 flex flex-col bg-[#2d2f31] border-l border-white/10 overflow-hidden"
        >
            {{-- Panel header --}}
            <div class="flex items-center justify-between px-4 pt-4 pb-3 border-b border-white/10 shrink-0">
                <div class="flex gap-1 bg-[#3c4043] p-1 rounded-lg">
                    <button
                        @click="panelTab = 'people'"
                        :class="panelTab === 'people' ? 'bg-[#5f6368] text-white' : 'text-[#9aa0a6] hover:text-white'"
                        class="text-xs font-medium px-3 py-1.5 rounded-md transition-colors"
                    >People</button>
                    @if($this->canManageMeeting())
                        <button
                            @click="panelTab = 'invite'"
                            :class="panelTab === 'invite' ? 'bg-[#5f6368] text-white' : 'text-[#9aa0a6] hover:text-white'"
                            class="text-xs font-medium px-3 py-1.5 rounded-md transition-colors"
                        >Invite</button>
                    @endif
                </div>
                <button
                    @click="showPanel = false"
                    class="w-8 h-8 rounded-full bg-[#3c4043] hover:bg-[#5f6368] flex items-center justify-center text-[#9aa0a6] hover:text-white transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- People tab --}}
            <div x-show="panelTab === 'people'" class="flex-1 overflow-y-auto py-3 px-4 space-y-1">
                {{-- Self --}}
                <div class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-white/5 transition-colors">
                    <div class="w-9 h-9 rounded-full bg-[#8ab4f8] flex items-center justify-center text-[#202124] font-semibold text-sm shrink-0">
                        {{ strtoupper(substr(auth()->user()?->first_name ?? '?', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-white text-sm truncate">{{ trim((auth()->user()?->first_name ?? '') . ' ' . (auth()->user()?->last_name ?? '')) ?: auth()->user()?->email }}
                            <span class="text-[#9aa0a6] font-normal">(you)</span>
                        </p>
                        <p class="text-[#9aa0a6] text-xs truncate">Host</p>
                    </div>
                    <div class="ml-auto flex items-center gap-1.5 shrink-0">
                        <span x-show="!micOn">
                            <svg class="w-3.5 h-3.5 text-red-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3.53 2.47a.75.75 0 00-1.06 1.06l18 18a.75.75 0 101.06-1.06l-18-18zM9 4.5a3 3 0 015.47-1.68L9.17 7.65A3 3 0 019 6.75V4.5zm-3 7.5a6 6 0 001.42 3.9L5.56 13.56A7.46 7.46 0 016 12zm6 6a6 6 0 01-5.97-5.47l-1.5-1.5A7.5 7.5 0 0012 19.5a7.5 7.5 0 007.5-7.5h-1.5A6 6 0 0112 18z"/>
                            </svg>
                        </span>
                        <span x-show="!camOn">
                            <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25zM2.25 3l18 18"/>
                            </svg>
                        </span>
                    </div>
                </div>

                {{-- Remote participants --}}
                <template x-for="p in participants" :key="p.userId">
                    <div class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-white/5 transition-colors">
                        <div class="w-9 h-9 rounded-full bg-[#5f6368] flex items-center justify-center text-white font-semibold text-sm shrink-0"
                             x-text="p.name.charAt(0).toUpperCase()"></div>
                        <p class="text-white text-sm truncate min-w-0" x-text="p.name"></p>
                    </div>
                </template>

                {{-- Invited (not yet joined) --}}
                @foreach($this->attendees() as $attendee)
                    @if($attendee->id !== auth()->id())
                        <div class="flex items-center gap-3 rounded-xl px-3 py-2.5 opacity-50">
                            <div class="w-9 h-9 rounded-full bg-[#3c4043] flex items-center justify-center text-[#9aa0a6] font-semibold text-sm shrink-0">
                                {{ strtoupper(substr($attendee->first_name ?? $attendee->email, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-white text-sm truncate">{{ trim(($attendee->first_name ?? '') . ' ' . ($attendee->last_name ?? '')) ?: $attendee->email }}</p>
                                <p class="text-[#9aa0a6] text-xs">Invited · not yet joined</p>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Invite tab --}}
            @if($this->canManageMeeting())
                <div x-show="panelTab === 'invite'" class="flex-1 flex flex-col px-4 py-4 gap-4 overflow-y-auto">
                    <p class="text-[#9aa0a6] text-xs">Select project members to invite to this meeting.</p>
                    <x-filament::input.wrapper class="bg-[#3c4043] border-white/10">
                        <x-filament::input.select
                            wire:model="selectedInvitees"
                            multiple
                            class="bg-[#3c4043] text-white text-sm"
                            style="min-height: 160px;"
                        >
                            @foreach($this->inviteOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                    <button
                        wire:click="inviteSelectedUsers"
                        class="w-full bg-[#8ab4f8] hover:bg-[#aecbfa] text-[#202124] font-medium py-2.5 rounded-xl text-sm transition-colors"
                    >
                        Send invites
                    </button>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
