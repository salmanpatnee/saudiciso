@props([
    'action_col' => 'false',
])

<thead style="position: sticky; top: 0; z-index: 50;">
    <tr style="background-color: #00053C; border-bottom: 2px solid rgba(202,164,27,.5); text-align: left; color: white;">

        {{ $slot }}
    </tr>
</thead>
