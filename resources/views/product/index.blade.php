@extends('app')

@section('body')
    <div class="bg-white max-w-2xl mx-auto shadow sm:rounded-sm p-4 text-sm">
        <h1 class="text-lg font-normal leading-7 text-gray-600 sm:leading-9 sm:truncate">
            Products
        </h1>
        @if($products->isEmpty())
            <p>Currently you do not have any products.<br />Please seed database.</p>
        @else
            <ul>
                @foreach($products as $product)
                    <li class="block py-2 border-t border-solid border-gray-300">
                        <a href="{{ route('product.view', $product->id) }}" class="text-blue-800 hover:text-blue-500">
                            {{ $product->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
