@if($record)

        <livewire:nested-comments::comments :record="$record" :key="'comments-'.$record->id"/>

@endif