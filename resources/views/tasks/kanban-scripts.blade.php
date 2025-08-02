<script>
    document.addEventListener('DOMContentLoaded', () => {
        const setupKanban = () => {
            const componentEl = document.querySelector('[wire\\:id]');
            if (!componentEl) return;
            const component = Livewire.find(componentEl.getAttribute('wire:id'));

            const destroyAllSortables = () => {
                document.querySelectorAll('[data-status-id]').forEach(el => {
                    if (el._sortable) {
                        el._sortable.destroy();
                        delete el._sortable;
                    }
                });
                const board = document.getElementById('kanban-statuses');
                if (board && board._sortable) {
                    board._sortable.destroy();
                    delete board._sortable;
                }
            };

            const initTaskSortables = () => {
                document.querySelectorAll('[data-status-id]').forEach(container => {
                    const statusId = container.dataset.statusId;
                    if (!statusId) return;

                    container._sortable = Sortable.create(container, {
                        group: 'filament-kanban',
                        animation: 150,
                        ghostClass: 'opacity-50',
                        onStart() {
                            document.body.classList.add('grabbing');
                        },
                        onEnd() {
                            document.body.classList.remove('grabbing')
                            window.dispatchEvent(new CustomEvent('livewire:loading'))

                            // You can clear it later (or rely on Livewire hooks to do it)
                            setTimeout(() => {
                                window.dispatchEvent(new CustomEvent('livewire:load'))
                            }, 1000)
                        },
                        setData(dataTransfer, el) {
                            dataTransfer.setData('id', el.id);
                        },
                        onAdd(evt) {
                            const recordId = evt.item.id;
                            const toStatus = evt.to.dataset.statusId;
                            const fromOrderedIds = Array.from(evt.from.children).map(el => el.id);
                            const toOrderedIds = Array.from(evt.to.children).map(el => el.id);
                            component.call('onStatusChanged', recordId, toStatus, fromOrderedIds, toOrderedIds);
                        },
                        onUpdate(evt) {
                            const recordId = evt.item.id;
                            const status = evt.from.dataset.statusId;
                            const orderedIds = Array.from(evt.from.children).map(el => el.id);
                            component.call('onSortChanged', recordId, status, orderedIds);
                        }
                    });
                });
            };

            const initStatusSortable = () => {
                const board = document.getElementById('kanban-statuses');
                if (!board) return;

                board._sortable = Sortable.create(board, {
                    animation: 150,
                    handle: '.status-header',
                    draggable: '.status-column',
                    onEnd() {
                        const orderedIds = Array.from(board.querySelectorAll('.status-column')).map(col => col.dataset.id);
                        component.call('updateStatusOrder', orderedIds);

                        // Wait for Livewire to re-render before reinitializing
                        setTimeout(() => {
                            destroyAllSortables();
                            initStatusSortable();
                            initTaskSortables();
                        }, 150);
                    }
                });
            };

            // Initial load
            destroyAllSortables();
            initStatusSortable();
            initTaskSortables();

            // Fix: Wait for DOM commit and reinit
            Livewire.hook('commit', () => {
                setTimeout(() => {
                    destroyAllSortables();
                    initStatusSortable();
                    initTaskSortables();
                }, 150);
            });
        };

        // Livewire navigation
        document.addEventListener('livewire:navigated', setupKanban);

        // Page already loaded
        setupKanban();
    });
</script>
