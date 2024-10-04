{{-- resources/views/products/pos.blade.php --}}
@extends('layouts.pos')
@section('content')
    <div class="container-fluid">
        <h1 class="mt-4">Consumable Products</h1>

        <!-- Add your search form here -->

        @if ($products->isEmpty())
            <div class="alert alert-secondary alert-icon" role="alert">
                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                <h6 class="alert-heading">Ooops, Nothing Here</h6>
                Search again with something new
            </div>
        @else
            <div class="row">
                @foreach ($products as $product)
                    <div class="col-md-3 mb-4">
                        <div class="card">
                            <div class="row g-0">
                                <div class="col-md-6">
                                    <img src="{{ asset($product->url_images) ?? 'default_image_url.jpg' }}" class="card-img"
                                        alt="{{ $product->name }}">
                                </div>
                                <div class="col-md-6">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $product->name }}</h5>
                                        <p class="card-text">Stock: {{ $product->stock }}</p>
                                        <a href="#" class="btn btn-sm btn-secondary add-to-cart"
                                            data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                            data-price="{{ $product->price }}">Consume</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <!-- Order Summary -->
                <h3 class="mt-4">Your Cart</h3>
                <div id="cart-summary">
                    <ul id="cart-items" class="list-group mb-4"></ul>
                    <p id="total-amount"></p>
                    <button class="btn btn-primary place-order">Place Order</button>
                </div>
            </div>
        @endif
    </div>

    <script>
        let cart = [];

        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const productId = this.getAttribute('data-id');
                const productName = this.getAttribute('data-name');
                const productPrice = parseFloat(this.getAttribute('data-price'));

                const existingItem = cart.find(item => item.product_id === productId);
                if (existingItem) {
                    existingItem.quantity += 1; // Increase quantity if already in cart
                } else {
                    cart.push({
                        product_id: productId,
                        name: productName,
                        quantity: 1,
                        price: productPrice
                    });
                }

                updateCartDisplay();
            });
        });

        function updateCartDisplay() {
            const cartItemsContainer = document.getElementById('cart-items');
            cartItemsContainer.innerHTML = ''; // Clear existing items
            let totalAmount = 0;

            cart.forEach(item => {
                const listItem = document.createElement('li');
                listItem.className = 'list-group-item';
                listItem.textContent = `${item.name} - $${item.price} x ${item.quantity}`;
                cartItemsContainer.appendChild(listItem);
                totalAmount += item.price * item.quantity;
            });

            document.getElementById('total-amount').textContent = `Total: $${totalAmount.toFixed(2)}`;
        }

        document.querySelector('.place-order').addEventListener('click', function() {
            if (cart.length === 0) {
                alert('Your cart is empty.');
                return;
            }

            const totalAmount = cart.reduce((total, item) => total + (item.price * item.quantity), 0);

            fetch('/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        items: cart,
                        total_amount: totalAmount
                    })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    alert('Order placed successfully!');
                    console.log('Order:', data.order);
                    cart = []; // Clear the cart after successful order
                    updateCartDisplay(); // Update cart display
                })
                .catch(error => {
                    console.error('Error placing order:', error);
                });
        });
    </script>
@endsection
