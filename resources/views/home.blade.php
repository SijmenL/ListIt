@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center p-4">
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

            <h1>Welcome, {{ auth()->user()->username }}</h1>
            <p>You have {{ $list_count }} lists and {{ $product_count }} products!</p>
            <div class="d-flex flex-row justify-content-between">
                <h2>Your Lists</h2>
                <a style="text-decoration: none" href="{{ route('list.create') }}">+ Create new</a>
            </div>
                <p>To be able to create a list, you need to have at least two products.</p>
                @if($list_count > 0)
                <table class="table table-striped">
                    <thead class="thead-dark table-bordered table-hover">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Description</th>
                        <th scope="col">Options</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach ($own_lists as $list)
                        <tr id="{{ $list->id }}">
                            <th>{{ $list->id }}</th>
                            <th>{{ $list->name }}</th>
                            <th>{{ $list->description }}</th>
                            <th>
                                <a href="{{ route('list.view', ['id' => $list->id]) }}"
                                   class="btn btn-secondary">View list</a>
                                <a href="{{ route('list.edit', ['id' => $list->id]) }}"
                                   class="btn btn-outline-warning">Edit</a>
                                <a class="delete-button btn btn-outline-danger"
                                   data-id="{{ $list->id }}" data-link="{{ route('list.delete', $list->id) }}">Delete</a>
                            </th>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @else
                    <div class="alert alert-warning" role="alert">
                        You have no lists, try creating one!
                    </div>

                @endif
            <div class="d-flex flex-row justify-content-between mt-5">
                <h2>Your Products</h2>
                <a style="text-decoration: none" href="{{ route('product.create') }}">+ Create new</a>
            </div>
                @if($product_count > 0)
            <div class="overflow-scroll" style="overflow: scroll; max-width: 100vw">
                <table class="table table-striped">
                    <thead class="thead-dark table-bordered table-hover">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Image</th>
                        <th scope="col">Title</th>
                        <th scope="col">Description</th>
                        <th scope="col">Tags</th>
                        <th scope="col">Options</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach ($own_products as $own_product)
                        <tr id="{{ $own_product->id }}">
                            <th>{{ $own_product->id }}</th>
                            <th><img class="card-img-top p-1"
                                     style="object-fit: cover; aspect-ratio: 1/1; height: 50%; min-width: 50px; max-width: 150px; border-radius: 15px;"
                                     src="{{ asset('product_images/' . $own_product['image'])}}"
                                     alt="Card image cap"></th>

                            <td>{{ $own_product->title }}</td>
                            <td>{{ $own_product->description }}</td>
                            <td>
                                <p class="card-header-pills d-flex flex-row gap-1 overflow-scroll">
                                    @foreach ($own_product->tags as $tag)
                                        <span class="badge bg-dark-subtle">{{ $tag->name }}</span>
                                    @endforeach
                                </p>
                            </td>
                            <td>
                                <a href="{{ route('product.addtolist', $own_product->id) }}"
                                   class="btn btn-secondary">Add to list</a>
                                <a href="{{ route('product.show', ['id' => $own_product->id]) }}"
                                   class="btn btn-outline-success">View
                                    product</a>
                                <a href="{{ route('product.edit', ['id' => $own_product->id]) }}"
                                   class="btn btn-outline-warning">Edit</a>
                                <a class="delete-button btn btn-outline-danger"
                                   data-id="{{ $own_product->id }}" data-link="{{ route('product.delete', $own_product->id) }}">Delete</a>
                                <form method="POST" action="{{ route('home.updatePrivateState') }}">
                                    @csrf
                                    <div class="d-flex flex-row gap-2  align-items-center mt-2">
                                        <label for="private-{{ $own_product->id }}">Private</label>
                                        <input type="hidden" name="id" value="{{ $own_product->id }}">
                                        <label class="switch">
                                            <input onchange="this.form.submit()" id="private-{{ $own_product->id }}"
                                                   type="checkbox"
                                                   name="private" {{ $own_product->private ? 'checked' : '' }}>
                                            <span class="slider round"></span>

                                        </label>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
                @else
                    <div class="alert alert-warning" role="alert">
                       You have no products.
                    </div>
                @endif
        </div>
    </div>
@endsection
