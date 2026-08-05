@extends('layouts.checkout')
@section('title', 'Endereço de Entrega | Dias Sneakers')
@section('step1_class', 'done') @section('step1_num', '✓')
@section('step2_class', 'done') @section('step2_num', '✓')
@section('step3_class', 'active') @section('step3_num', '3')
@section('step4_num', '4')
@section('content')

<div class="checkout-wrapper">
  <div class="checkout-main">

    <div class="checkout-section-title">Endereço de Entrega</div>

    @if($errors->any())
      <div style="background:#fff5f5;color:#c53030;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:.85rem;border:1px solid #fed7d7">
        @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
      </div>
    @endif

    <form action="{{ route('checkout.address.save') }}" method="POST" id="address-form">
      @csrf

      <div class="form-row">
        <div class="form-field" style="flex:1">
          <label>Flat / Villa No *</label>
          <input type="text" name="cep" id="cep" value="{{ old('cep', $address['cep'] ?? '') }}"
            placeholder="Ex: Apt 204, Villa 12" maxlength="30" required>
        </div>
        <div class="form-field" style="flex:1">
          <label>Building Name *</label>
          <input type="text" name="number" id="number" value="{{ old('number', $address['number'] ?? '') }}"
            placeholder="Ex: Marina Crown Tower" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-field" style="flex:2">
          <label>Street / Road *</label>
          <input type="text" name="street" id="street" value="{{ old('street', $address['street'] ?? '') }}"
            placeholder="Ex: Sheikh Zayed Road" required>
        </div>
        <div class="form-field" style="flex:1">
          <label>Area / District *</label>
          <input type="text" name="neighborhood" id="neighborhood" value="{{ old('neighborhood', $address['neighborhood'] ?? '') }}"
            placeholder="Ex: Downtown Dubai, Marina, JBR" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-field">
          <label>Additional Directions <span style="font-weight:400;text-transform:none;letter-spacing:0">(optional)</span></label>
          <input type="text" name="complement" id="complement" value="{{ old('complement', $address['complement'] ?? '') }}"
            placeholder="Ex: Near Dubai Mall, Blue building">
        </div>
      </div>

      <div class="form-row">
        <div class="form-field" style="flex:2">
          <label>City *</label>
          <input type="text" name="city" id="city" value="{{ old('city', $address['city'] ?? 'Dubai') }}"
            placeholder="Dubai" required>
        </div>
        <div class="form-field" style="flex:0 0 180px">
          <label>Emirate *</label>
          <select name="state" id="state" required>
            <option value="">Select</option>
            @foreach(['Dubai','Abu Dhabi','Sharjah','Ajman','Ras Al Khaimah','Fujairah','Umm Al Quwain'] as $emirate)
              <option value="{{ $emirate }}"
                {{ old('state', $address['state'] ?? 'Dubai') === $emirate ? 'selected' : '' }}>
                {{ $emirate }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      @auth
      <div style="display:flex;align-items:center;gap:10px;margin-top:8px;padding:14px 16px;background:#f8f8f8;border:1px solid #e5e5e5;border-radius:8px">
        <input type="checkbox" name="save_address" id="save_address" value="1"
          style="width:18px;height:18px;accent-color:#000;cursor:pointer;flex-shrink:0">
        <label for="save_address" style="font-size:.85rem;color:#444;cursor:pointer;font-weight:500;margin:0">
          Salvar como endereço padrão de entrega
        </label>
      </div>
      @endauth

      <button type="submit" class="btn-checkout-continue" style="margin-top:28px">
        Continuar para Pagamento
      </button>
    </form>
  </div>

  <div class="checkout-sidebar">
    <div class="co-summary" style="border:1.5px solid #e0e0e0;background:#fafafa">
      <div class="co-summary-title" style="font-size:.78rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;padding:18px 20px 14px;border-bottom:1px solid #ebebeb">
        Resumo do Pedido
      </div>
      <div style="padding:14px 20px;display:flex;flex-direction:column;gap:10px;border-bottom:1px solid #ebebeb">
        @foreach(session('cart', []) as $item)
        <div style="display:flex;justify-content:space-between;font-size:.83rem;color:#444;gap:8px">
          <span style="flex:1;min-width:0">{{ $item['product_name'] }} <span style="color:#aaa">Tam: {{ $item['size'] }}</span> × {{ $item['qty'] }}</span>
          <span style="font-weight:600;white-space:nowrap">AED {{ number_format($item['price']*$item['qty'],2,'.',',') }}</span>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>

@endsection
@push('styles')
<style>
.form-field select {
  width:100%;
  border:1.5px solid #e0e0e0;
  padding:12px 14px;
  font-family:'Inter',sans-serif;
  font-size:.88rem;
  background:#fff;
  outline:none;
  appearance:none;
  background-image:url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23999' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E");
  background-repeat:no-repeat;
  background-position:right 14px center;
  cursor:pointer;
  transition:border-color .18s;
}
.form-field select:focus { border-color:#000; }
</style>
@endpush
