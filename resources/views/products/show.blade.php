@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center p-4">
            <h1>Product {{ $product->title }}</h1>
            @if(Session::has('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            @if(Session::has('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="d-flex flex-row gap-2">
                <div class="card w-25">
                    <img class="card-img-top p-5" src="{{ asset('product_images/' . $product['image'])}}"
                         alt="Card image cap">
                    <div class="card-body">
                        <h2 class="card-title">{{ $product->title }}</h2>
                        <p class="card-text">{{ $product->subtitle }} | {{ $product->tag_id}}</p>
                        <p class="card-header-pills d-flex flex-row gap-1 overflow-scroll">
                            @foreach ($product->tags as $tag)
                                <span class="badge bg-dark-subtle">{{ $tag->name }}</span>
                            @endforeach
                        </p>
                        <p class="card-text">{{ $product->valuta . '' . $product->price }}</p>

                        <p class="card-text">{{ $product->description }}</p>
                        <p class="card-text">Product owned by {{ $ownerUsername }}</p>

                        <div class="d-flex flex-row flex-wrap gap-2">
                            <a href="{{ route('product.addtolist', $product->id) }}"
                               class="btn btn-secondary">Add to list</a>

                            @if($product->product_owner === Auth::id())
                                <a href="{{ route('product.edit', ['id' => $product->id]) }}"
                                   class="btn btn-outline-warning">Edit</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div>
                </div>
            </div>
@endsection
