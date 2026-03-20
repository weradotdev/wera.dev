<x-filament-panels::page>
    <div
        class="space-y-6"
        x-data="{
            meetingId: @js($this->meeting?->getKey()),
            currentUserId: @js((int) auth()->id()),
            localStream: null,
            peers: {},

            async initRoom() {
                if (!this.meetingId || !window.Echo) {
                    return;
                }

                window.Echo.private(`meetings.${this.meetingId}`)
                    .listen('.meeting.signal', async (event) => {
                        if (!event || event.from_user_id === this.currentUserId) {
                            return;
                        }

                        if (event.to_user_id && event.to_user_id !== this.currentUserId) {
                            return;
                        }

                        if (event.type === 'join') {
                            await this.callUser(event.from_user_id);
                            return;
                        }

                        if (event.type === 'leave') {
                            this.closePeer(event.from_user_id);
                            return;
                        }

                        await this.handleSignal(event.from_user_id, event.type, event.payload || {});
                    });

                await $wire.sendSignal('join', {});
            },

            async startCamera() {
                if (this.localStream) {
                    return;
                }

                this.localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                this.$refs.localVideo.srcObject = this.localStream;
            },

            createPeer(targetUserId) {
                if (this.peers[targetUserId]) {
                    return this.peers[targetUserId];
                }

                const peer = new RTCPeerConnection({
                    iceServers: [{ urls: 'stun:stun.l.google.com:19302' }],
                });

                if (this.localStream) {
                    this.localStream.getTracks().forEach((track) => {
                        peer.addTrack(track, this.localStream);
                    });
                }

                peer.onicecandidate = (e) => {
                    if (!e.candidate) {
                        return;
                    }

                    $wire.sendSignal('ice-candidate', { candidate: e.candidate }, Number(targetUserId));
                };

                peer.ontrack = (e) => {
                    this.$refs.remoteVideo.srcObject = e.streams[0];
                };

                this.peers[targetUserId] = peer;
                return peer;
            },

            async callUser(targetUserId) {
                const peer = this.createPeer(targetUserId);
                const offer = await peer.createOffer();
                await peer.setLocalDescription(offer);
                await $wire.sendSignal('offer', { sdp: offer }, Number(targetUserId));
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

            closePeer(targetUserId) {
                if (!this.peers[targetUserId]) {
                    return;
                }

                this.peers[targetUserId].close();
                delete this.peers[targetUserId];
            },

            async callAll() {
                const userIds = @js($this->attendees()->pluck('id')->values()->all());

                for (const userId of userIds) {
                    if (userId === this.currentUserId) {
                        continue;
                    }

                    await this.callUser(userId);
                }
            },

            async leaveRoom() {
                await $wire.sendSignal('leave', {});
                await $wire.markLeft();

                Object.keys(this.peers).forEach((id) => this.closePeer(id));

                if (this.localStream) {
                    this.localStream.getTracks().forEach((track) => track.stop());
                }

                this.localStream = null;
            }
        }"
        x-init="initRoom()"
    >
        <div class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <p class="mb-3 text-sm font-medium text-gray-600 dark:text-gray-300">Your camera</p>
                <video x-ref="localVideo" autoplay playsinline muted class="aspect-video w-full rounded-lg bg-black/90"></video>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <p class="mb-3 text-sm font-medium text-gray-600 dark:text-gray-300">Remote stream</p>
                <video x-ref="remoteVideo" autoplay playsinline class="aspect-video w-full rounded-lg bg-black/90"></video>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <x-filament::button color="gray" x-on:click="startCamera()">Start Camera</x-filament::button>
            <x-filament::button color="primary" x-on:click="callAll()">Call Attendees</x-filament::button>
            <x-filament::button color="danger" x-on:click="leaveRoom()">Leave Meeting</x-filament::button>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Attendees</h3>
                <div class="space-y-2">
                    @foreach($this->attendees() as $attendee)
                        <div class="flex items-center justify-between rounded-lg border border-gray-100 px-3 py-2 text-sm dark:border-gray-700">
                            <span>{{ trim($attendee->first_name . ' ' . $attendee->last_name) ?: $attendee->email }}</span>
                            <span class="text-xs text-gray-500">{{ $attendee->email }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            @if($this->canManageMeeting())
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Invite People</h3>

                    <div class="space-y-3">
                        <x-filament::input.wrapper>
                            <x-filament::input.select wire:model="selectedInvitees" multiple>
                                @foreach($this->inviteOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </x-filament::input.select>
                        </x-filament::input.wrapper>

                        <x-filament::button wire:click="inviteSelectedUsers">
                            Invite Selected
                        </x-filament::button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
