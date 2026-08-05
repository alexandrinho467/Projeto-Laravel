@extends('layouts.admin')
@section('title', 'Gerenciar Estoque | Admin')
@section('content')

@php $img = $product->images->first(); @endphp

<div class="admin-topbar">
  <div style="display:flex;align-items:center;gap:16px">
    @if($img)
      <img src="{{ asset($img->img1) }}" style="width:52px;height:52px;object-fit:cover;border-radius:8px">
    @endif
    <div>
      <div style="font-size:.8rem;color:#697386;margin-bottom:2px">{{ $product->brand }}</div>
      <h1 class="admin-title" style="margin:0">{{ $product->name }}</h1>
    </div>
  </div>
  <a href="{{ route('admin.estoque') }}" class="btn-secondary">← Voltar ao estoque</a>
</div>

@if($errors->any())
  <div class="alert-error">{{ $errors->first() }}</div>
@endif

{{-- Forms de remoção fora do form principal --}}
@foreach($product->sizes as $ps)
  <form id="remove-form-{{ $ps->id }}"
        action="{{ route('admin.estoque.remove', $ps) }}"
        method="POST" style="display:none">
    @csrf @method('DELETE')
  </form>
@endforeach

<div style="display:grid;grid-template-columns:1fr 320px;gap:32px;align-items:start">

  {{-- Tamanhos existentes --}}
  <div>
    <h2 style="font-size:1rem;font-weight:600;color:#1A1F36;margin-bottom:16px">Tamanhos cadastrados</h2>

    @if($product->sizes->isEmpty())
      <div style="color:#697386;padding:32px;text-align:center;border:1px dashed #D1D5DB;border-radius:8px">
        Nenhum tamanho cadastrado. Adicione o primeiro pelo painel ao lado.
      </div>
    @else
      <form id="save-form" action="{{ route('admin.estoque.update', $product) }}" method="POST">
        @csrf

        <table class="admin-table">
          <thead>
            <tr>
              <th>Tamanho</th>
              <th>Quantidade em estoque</th>
              <th>Alerta quando ≤</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($product->sizes as $ps)
            <tr>
              <td>
                <input type="hidden" name="sizes[{{ $loop->index }}][id]" value="{{ $ps->id }}">
                <span style="font-size:1rem;font-weight:700;color:#1A1F36">{{ $ps->size }}</span>
              </td>
              <td>
                <div style="display:flex;align-items:center;gap:10px">
                  <button type="button" onclick="changeQty({{ $loop->index }}, -1)"
                    style="width:28px;height:28px;border-radius:6px;background:#F3F4F6;border:1px solid #E3E8EE;color:#1A1F36;font-size:1.1rem;cursor:pointer;line-height:1">−</button>
                  <input type="number" name="sizes[{{ $loop->index }}][stock]" value="{{ $ps->stock }}"
                    id="qty-{{ $loop->index }}" min="0"
                    style="width:70px;text-align:center;background:#fff;border:1px solid #D1D5DB;color:#1A1F36;padding:6px;border-radius:6px;font-size:.95rem;font-weight:600">
                  <button type="button" onclick="changeQty({{ $loop->index }}, 1)"
                    style="width:28px;height:28px;border-radius:6px;background:#F3F4F6;border:1px solid #E3E8EE;color:#1A1F36;font-size:1.1rem;cursor:pointer;line-height:1">+</button>
                </div>
              </td>
              <td>
                <input type="number" name="sizes[{{ $loop->index }}][stock_alert]"
                  value="{{ $ps->stock_alert ?? 1 }}" min="0"
                  style="width:60px;text-align:center;background:#fff;border:1px solid #D1D5DB;color:#92400E;padding:6px;border-radius:6px;font-size:.88rem;font-weight:600"
                  title="Alerta de estoque baixo">
              </td>
              <td>
                @if($ps->stock > ($ps->stock_alert ?? 1))
                  <span class="badge badge-green">{{ $ps->stock }} em estoque</span>
                @elseif($ps->stock > 0)
                  <span class="badge badge-yellow">Estoque baixo ({{ $ps->stock }})</span>
                @else
                  <span class="badge badge-red">Esgotado</span>
                @endif
              </td>
              <td>
                <button type="submit"
                        form="remove-form-{{ $ps->id }}"
                        class="btn-danger"
                        style="padding:5px 10px;font-size:.75rem"
                        onclick="return confirm('Remover tamanho {{ $ps->size }}?')">
                  Remover
                </button>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>

        <div style="margin-top:20px">
          <button type="submit" class="btn-primary">Salvar estoque</button>
        </div>
      </form>
    @endif
  </div>

  {{-- Adicionar novo tamanho --}}
  <div style="background:#fff;border-radius:8px;padding:24px;border:1px solid #E3E8EE">
    <h2 style="font-size:1rem;font-weight:600;color:#1A1F36;margin-bottom:20px">Adicionar tamanho</h2>

    <form action="{{ route('admin.estoque.add', $product) }}" method="POST">
      @csrf

      <div class="form-group">
        <label>Tamanho</label>
        <input type="text" name="size" value="{{ old('size') }}" placeholder="Ex: 42" maxlength="10" required>
      </div>

      <div class="form-group">
        <label>Quantidade</label>
        <input type="number" name="stock" value="{{ old('stock', 1) }}" min="0" required>
      </div>

      <button type="submit" class="btn-primary" style="width:100%">Adicionar</button>
    </form>

    @if($product->sizes->isNotEmpty())
      <div style="margin-top:24px;padding-top:20px;border-top:1px solid #E3E8EE">
        <div style="font-size:.72rem;color:#697386;margin-bottom:10px;text-transform:uppercase;letter-spacing:.06em;font-weight:600">Resumo</div>
        <div style="display:flex;justify-content:space-between;font-size:.85rem;color:#697386;margin-bottom:6px">
          <span>Total de tamanhos</span>
          <span style="color:#1A1F36;font-weight:600">{{ $product->sizes->count() }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:.85rem;color:#697386;margin-bottom:6px">
          <span>Total em estoque</span>
          <span style="color:#059669;font-weight:600">{{ $product->sizes->sum('stock') }} pares</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:.85rem;color:#697386">
          <span>Tamanhos esgotados</span>
          <span style="color:{{ $product->sizes->where('stock', 0)->count() > 0 ? '#991B1B' : '#059669' }};font-weight:600">
            {{ $product->sizes->where('stock', 0)->count() }}
          </span>
        </div>
      </div>
    @endif
  </div>

</div>

@push('scripts')
<script>
function changeQty(index, delta) {
  const input = document.getElementById('qty-' + index);
  const newVal = Math.max(0, parseInt(input.value || 0) + delta);
  input.value = newVal;
}
</script>
@endpush
@endsection
