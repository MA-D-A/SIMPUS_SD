<a href="{{ request()->fullUrlWithQuery(['format' => 'cetak', 'page' => null]) }}" target="_blank" class="btn btn-secondary">Cetak</a>
<a href="{{ request()->fullUrlWithQuery(['format' => 'pdf', 'page' => null]) }}" class="btn btn-secondary">PDF</a>
<a href="{{ request()->fullUrlWithQuery(['format' => 'excel', 'page' => null]) }}" class="btn btn-secondary">Excel</a>
