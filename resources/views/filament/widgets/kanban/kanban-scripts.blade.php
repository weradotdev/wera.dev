<script>
    function onStart() {
        setTimeout(() => document.body.classList.add("grabbing"))
    }

    function onEnd() {
        document.body.classList.remove("grabbing")
    }

    function setData(dataTransfer, el) {
        dataTransfer.setData('id', el.id)
    }

    function onAdd(e) {
        const recordId = e.item.id
        const status = e.to.dataset.statusId
        const fromOrderedIds = [].slice.call(e.from.children).map(child => child.id)
        const toOrderedIds = [].slice.call(e.to.children).map(child => child.id)

        Livewire.dispatch('status-changed', {recordId, status, fromOrderedIds, toOrderedIds})
    }

    function onUpdate(e) {
        const recordId = e.item.id
        const status = e.from.dataset.statusId
        const orderedIds = [].slice.call(e.from.children).map(child => child.id)

        Livewire.dispatch('sort-changed', {recordId, status, orderedIds})
    }

    let sortableInstances = []

    function initSortables() {
        if (typeof Sortable === 'undefined') return
        sortableInstances.forEach(s => s.destroy())
        sortableInstances = []

        const statuses = @js($statuses->pluck('id')->values()->toArray());

        statuses.forEach(statusId => {
            const el = document.querySelector(`[data-status-id='${statusId}']`)
            if (el) {
                sortableInstances.push(Sortable.create(el, {
                    group: 'filament-kanban',
                    ghostClass: 'opacity-50',
                    animation: 150,
                    onStart,
                    onEnd,
                    onUpdate,
                    setData,
                    onAdd,
                }))
            }
        })
    }

    function initWhenReady() {
        if (typeof Sortable === 'undefined') {
            setTimeout(initWhenReady, 50)
            return
        }
        initSortables()
    }

    document.addEventListener('livewire:navigated', initWhenReady)
    document.addEventListener('livewire:load', initWhenReady)
    document.addEventListener('kanban-boards-updated', initWhenReady)
</script>
