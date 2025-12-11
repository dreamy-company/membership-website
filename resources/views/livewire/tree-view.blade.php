<div>
    <div class="p-6 bg-white rounded-lg shadow-sm">
        <div class="space-y-6">

            @foreach ($members as $member)
                @include('components.tree-node', ['node' => $member, 'isChild' => false])
            @endforeach

        </div>
    </div>
</div>
