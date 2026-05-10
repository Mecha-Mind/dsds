{{-- resources/views/components/page-header.blade.php --}}
@props([
    'title'       => '',
    'breadcrumbs' => [],
])

<div class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 text-center">
                <h1 class="page-header__title">{{ $title }}</h1>
                @if(count($breadcrumbs))
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center mb-0">
                        @foreach($breadcrumbs as $i => $crumb)
                        @if($i === count($breadcrumbs) - 1)
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ $crumb['name'] }}
                        </li>
                        @else
                        <li class="breadcrumb-item">
                            <a href="{{ $crumb['url'] }}">{{ $crumb['name'] }}</a>
                        </li>
                        @endif
                        @endforeach
                    </ol>
                </nav>
                @endif
            </div>
        </div>
    </div>
</div>