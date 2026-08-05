@extends('layouts.app')
@section('title', 'Marcas | Dias Sneakers')
@section('content')

<div class="breadcrumb">
  <a href="{{ route('home') }}">Home</a>
  <span class="bc-sep">›</span>
  <span class="bc-current">Marcas</span>
</div>

<section class="products-section" style="padding-top: 56px;">
  <div class="section-header">
    <h1 class="section-title">Nossas Marcas</h1>
    <p class="section-subtitle">Selecione uma marca para explorar</p>
  </div>

  <div class="brands-grid">
    @foreach($brands as $slug => $brand)
      <a href="{{ route('marca.show', $slug) }}" class="brand-card">
        @if($brand['logo'])
          <img src="{{ asset($brand['logo']) }}" alt="{{ $brand['label'] }}" class="brand-logo">
        @else
          <span class="brand-name-text">{{ $brand['label'] }}</span>
        @endif
      </a>
    @endforeach
  </div>
</section>

@endsection
