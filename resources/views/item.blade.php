@if($item->type == \App\Models\Container::CIRCULAR)
    Circle: Radius {{ $item->radius }}
@else
    Rectangle: width {{ $item->width }}, length {{ $item->length }}
@endif
