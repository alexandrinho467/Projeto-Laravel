@props(['value' => '', 'required' => false, 'name' => 'phone', 'id' => 'phone'])

<div class="pcd-wrap" id="pcd-wrap">
  <div class="pcd-field">
    <div class="pcd-trigger" id="pcd-trigger" type="button" onclick="pcdToggle(event)">
      <span class="pcd-selected-flag" id="pcd-flag">🇦🇪</span>
      <span class="pcd-selected-code" id="pcd-dial">+971</span>
      <svg class="pcd-chevron" width="10" height="6" viewBox="0 0 10 6" fill="none">
        <path d="M1 1l4 4 4-4" stroke="#aaa" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>
    <input type="tel" class="pcd-number" id="pcd-number"
      placeholder="XX XXX XXXX" maxlength="15"
      oninput="pcdSync()" autocomplete="tel">
    <input type="hidden" name="{{ $name }}" id="{{ $id }}" value="{{ $value }}"
      @if($required) required @endif>
  </div>

  <div class="pcd-dropdown" id="pcd-dropdown">
    <div class="pcd-search-wrap">
      <svg width="13" height="13" fill="none" stroke="#bbb" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0">
        <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
      </svg>
      <input type="text" class="pcd-search" id="pcd-search"
        placeholder="Search country or code..."
        oninput="pcdSearch(this.value)"
        onclick="event.stopPropagation()"
        autocomplete="off">
    </div>
    <div class="pcd-list" id="pcd-list"></div>
  </div>
</div>

<script>
(function () {
  const COUNTRIES = [
    { f:'🇦🇪', n:'UAE',             c:'+971' },
    { f:'🇸🇦', n:'Saudi Arabia',    c:'+966' },
    { f:'🇰🇼', n:'Kuwait',          c:'+965' },
    { f:'🇶🇦', n:'Qatar',           c:'+974' },
    { f:'🇧🇭', n:'Bahrain',         c:'+973' },
    { f:'🇴🇲', n:'Oman',            c:'+968' },
    { f:'🇯🇴', n:'Jordan',          c:'+962' },
    { f:'🇪🇬', n:'Egypt',           c:'+20'  },
    { f:'🇮🇳', n:'India',           c:'+91'  },
    { f:'🇵🇰', n:'Pakistan',        c:'+92'  },
    { f:'🇵🇭', n:'Philippines',     c:'+63'  },
    { f:'🇬🇧', n:'United Kingdom',  c:'+44'  },
    { f:'🇺🇸', n:'United States',   c:'+1'   },
    { f:'🇫🇷', n:'France',          c:'+33'  },
    { f:'🇩🇪', n:'Germany',         c:'+49'  },
    { f:'🇷🇺', n:'Russia',          c:'+7'   },
    { f:'🇨🇳', n:'China',           c:'+86'  },
    { f:'🇧🇷', n:'Brazil',          c:'+55'  },
  ];

  let selected = COUNTRIES[0];

  function render(list) {
    document.getElementById('pcd-list').innerHTML = list.map(c =>
      `<div class="pcd-option${c.c === selected.c ? ' active' : ''}" onclick="window.__pcdSelect('${c.f}','${c.n}','${c.c}')">
        <span class="pcd-opt-flag">${c.f}</span>
        <span class="pcd-opt-name">${c.n}</span>
        <span class="pcd-opt-code">${c.c}</span>
      </div>`
    ).join('');
  }

  function sync() {
    var num = document.getElementById('pcd-number').value.trim();
    document.getElementById('{{ $id }}').value = num ? selected.c + ' ' + num : '';
  }

  window.pcdSync   = sync;

  window.pcdToggle = function(e) {
    e && e.stopPropagation();
    const wrap = document.getElementById('pcd-wrap');
    const dd   = document.getElementById('pcd-dropdown');
    const open = wrap.classList.toggle('open');
    if (open) {
      document.getElementById('pcd-search').value = '';
      render(COUNTRIES);
      setTimeout(() => document.getElementById('pcd-search').focus(), 60);
    }
  };

  window.pcdSearch = function(q) {
    const t = q.toLowerCase();
    render(COUNTRIES.filter(c => c.n.toLowerCase().includes(t) || c.c.includes(q)));
  };

  window.__pcdSelect = function(f, n, c) {
    selected = { f, n, c };
    document.getElementById('pcd-flag').textContent = f;
    document.getElementById('pcd-dial').textContent = c;
    document.getElementById('pcd-wrap').classList.remove('open');
    sync();
    document.getElementById('pcd-number').focus();
  };

  // Parse existing value
  var existing = document.getElementById('{{ $id }}').value;
  if (existing) {
    var match = COUNTRIES.slice().sort((a,b) => b.c.length - a.c.length)
                         .find(c => existing.startsWith(c.c));
    if (match) {
      selected = match;
      document.getElementById('pcd-flag').textContent = match.f;
      document.getElementById('pcd-dial').textContent = match.c;
      document.getElementById('pcd-number').value = existing.slice(match.c.length).trim();
    } else {
      document.getElementById('pcd-number').value = existing;
    }
  }

  // Close on outside click
  document.addEventListener('click', function(e) {
    if (!document.getElementById('pcd-wrap').contains(e.target)) {
      document.getElementById('pcd-wrap').classList.remove('open');
    }
  });
})();
</script>
