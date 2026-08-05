<div class="form-row">
  <div class="form-group">
    <label>Marca *</label>
    <input type="text" name="brand" value="{{ old('brand', $product->brand ?? '') }}" required>
  </div>
  <div class="form-group">
    <label>Nome do Produto *</label>
    <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required>
  </div>
</div>

<div class="form-row">
  <div class="form-group">
    <label>Preço *</label>
    <input type="number" name="price" step="0.01" value="{{ old('price', $product->price ?? '') }}" required>
  </div>
  <div class="form-group">
    <label>Preço Original (De)</label>
    <input type="number" name="old_price" step="0.01" value="{{ old('old_price', $product->old_price ?? '') }}">
  </div>
</div>

<div class="form-row">
  <div class="form-group">
    <label>Badge</label>
    <input type="text" name="badge" value="{{ old('badge', $product->badge ?? '') }}" placeholder="Ex: Edição Limitada">
  </div>
  <div class="form-group">
    <label>Código de Referência</label>
    <input type="text" name="ref" value="{{ old('ref', $product->ref ?? '') }}">
  </div>
</div>

<div class="form-group">
  <label>Descrição</label>
  <textarea name="description" rows="4" style="resize:vertical">{{ old('description', $product->description ?? '') }}</textarea>
</div>

<div class="form-group">
  <label>Informações Técnicas</label>
  <textarea name="tech" rows="3" style="resize:vertical">{{ old('tech', $product->tech ?? '') }}</textarea>
</div>

<div class="form-group">
  <label>Características (uma por linha)</label>
  <textarea name="features[]" rows="6" id="features-field" style="resize:vertical">{{ old('features.0', isset($product) ? $product->features->pluck('feature')->join("\n") : '') }}</textarea>
  <div style="font-size:.78rem;color:#697386;margin-top:4px">Uma característica por linha</div>
</div>

<div class="form-group">
  <label>Ordem de Exibição</label>
  <input type="number" name="sort_order" value="{{ old('sort_order', $product->sort_order ?? 0) }}" style="max-width:120px">
</div>

<div class="form-group" style="display:flex;align-items:center;gap:10px">
  <input type="checkbox" name="active" value="1" id="active" {{ old('active', ($product->active ?? true) ? '1' : '0') == '1' ? 'checked' : '' }} style="width:auto">
  <label for="active" style="margin:0">Produto ativo</label>
</div>

<hr style="border-color:#E3E8EE;margin:28px 0">
<h3 style="margin:0 0 16px;font-size:1rem;font-weight:600">Imagens / Cores</h3>

<div id="colors-container">
  @if(isset($product) && $product->images->count() > 0)
    @foreach($product->images as $ci => $colorImg)
      <div class="color-row" style="background:#F7FAFC;border:1px solid #E3E8EE;border-radius:8px;padding:16px;margin-bottom:12px">
        <div class="form-row">
          <div class="form-group">
            <label>Nome da Cor</label>
            <input type="text" name="color_name[]" value="{{ $colorImg->color_name }}">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Imagem Principal (URL ou upload)</label>
            <input type="text" name="img1[]" value="{{ $colorImg->img1 }}" placeholder="assets/images/produto.png">
            <input type="file" name="img1_file[{{ $ci }}]" style="margin-top:6px" accept="image/*">
          </div>
          <div class="form-group">
            <label>Imagem Hover (URL ou upload)</label>
            <input type="text" name="img2[]" value="{{ $colorImg->img2 }}" placeholder="assets/images/produto-2.png">
            <input type="file" name="img2_file[{{ $ci }}]" style="margin-top:6px" accept="image/*">
          </div>
        </div>
      </div>
    @endforeach
  @else
    <div class="color-row" style="background:#F7FAFC;border:1px solid #E3E8EE;border-radius:8px;padding:16px;margin-bottom:12px">
      <div class="form-group"><label>Nome da Cor</label><input type="text" name="color_name[]"></div>
      <div class="form-row">
        <div class="form-group">
          <label>Imagem Principal</label>
          <input type="text" name="img1[]" placeholder="assets/images/produto.png">
          <input type="file" name="img1_file[0]" style="margin-top:6px" accept="image/*">
        </div>
        <div class="form-group">
          <label>Imagem Hover</label>
          <input type="text" name="img2[]" placeholder="assets/images/produto-2.png">
          <input type="file" name="img2_file[0]" style="margin-top:6px" accept="image/*">
        </div>
      </div>
    </div>
  @endif
</div>
<button type="button" onclick="addColor()" class="btn-secondary" style="margin-bottom:28px">+ Adicionar cor</button>

<hr style="border-color:#E3E8EE;margin:28px 0">
<h3 style="margin:0 0 16px;font-size:1rem;font-weight:600">Tamanhos Disponíveis</h3>
<div style="display:flex;flex-wrap:wrap;gap:10px">
  @php $existingSizes = isset($product) ? $product->sizes->where('available',true)->pluck('size')->toArray() : ['38','39','40','41','42','43','44']; @endphp
  @foreach(['35','36','37','38','39','40','41','42','43','44','45'] as $sz)
    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:.9rem">
      <input type="checkbox" name="sizes[]" value="{{ $sz }}" {{ in_array($sz, $existingSizes) ? 'checked' : '' }}>
      {{ $sz }}
    </label>
  @endforeach
</div>

@push('scripts')
<script>
let colorCount = {{ isset($product) ? $product->images->count() : 1 }};
function addColor() {
  const i = colorCount++;
  const html = `<div class="color-row" style="background:#F7FAFC;border:1px solid #E3E8EE;border-radius:8px;padding:16px;margin-bottom:12px">
    <div class="form-group"><label>Nome da Cor</label><input type="text" name="color_name[]"></div>
    <div class="form-row">
      <div class="form-group"><label>Imagem Principal</label><input type="text" name="img1[]" placeholder="assets/images/"><input type="file" name="img1_file[${i}]" style="margin-top:6px" accept="image/*"></div>
      <div class="form-group"><label>Imagem Hover</label><input type="text" name="img2[]" placeholder="assets/images/"><input type="file" name="img2_file[${i}]" style="margin-top:6px" accept="image/*"></div>
    </div>
  </div>`;
  document.getElementById('colors-container').insertAdjacentHTML('beforeend', html);
}

// Features: convert newlines to array
document.querySelector('form').addEventListener('submit', function() {
  const area = document.getElementById('features-field');
  const lines = area.value.split('\n').map(l=>l.trim()).filter(Boolean);
  area.remove();
  lines.forEach(line => {
    const inp = document.createElement('input');
    inp.type = 'hidden'; inp.name = 'features[]'; inp.value = line;
    this.appendChild(inp);
  });
});
</script>
@endpush
