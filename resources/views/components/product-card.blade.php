{{-- resources/views/components/product-card.blade.php --}}
@props(['product'])

<article class="product-card">

    @if(!empty($product['discount']))
    <span class="product-card__badge">{{ $product['discount'] }}%</span>
    @endif

    <a href="{{ route('product.show', $product['slug']) }}"
       class="product-card__img-wrap">
        <img src="{{ asset($product['image']) }}"
             alt="{{ $product['name'] }}"
             class="product-card__img"
             loading="lazy">
    </a>

    <div class="product-card__body">
        <h3 class="product-card__name">
            <a href="{{ route('product.show', $product['slug']) }}">
                {{ $product['name'] }}
            </a>
        </h3>

        <div class="product-card__prices">
            <span class="product-card__price">
                {{ number_format($product['price']) }} جنية
            </span>
            @if(!empty($product['old_price']))
            <span class="product-card__old-price">
                {{ number_format($product['old_price']) }}
            </span>
            @endif
        </div>

        <div class="product-card__actions">
            <button class="btn product-card__add-btn" type="button">
                <i class="bi bi-bag"></i>
                أضف الى السلة
            </button>
            <button class="btn product-card__wish-btn" type="button">
                <i class="bi bi-heart"></i>
            </button>
        </div>
    </div>

</article>