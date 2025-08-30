
<div class="section-card bg-white border border-teal-200 rounded-xl p-4 flex flex-col h-full">
	<div class="flex items-center mb-2">
		<div class="w-10 h-10 bg-teal-500 rounded-xl flex items-center justify-center mr-3">
			<i class="fas fa-shield-alt text-white text-xl"></i>
		</div>
		<div>
			<div class="text-teal-800 font-bold text-lg">Seguro</div>
			<div class="text-teal-600 text-xs">Proteção da viagem</div>
		</div>
		<div class="ml-auto text-xs text-teal-700 font-semibold">
			{{ is_countable($seguros) ? count($seguros) : 0 }} seguro{{ (is_countable($seguros) && count($seguros) == 1) ? '' : 's' }}
		</div>
	</div>
	<div class="flex-1 mt-2">
		@if(isset($seguros) && is_countable($seguros) && count($seguros) > 0)
			<ul class="space-y-2">
				@foreach($seguros as $seguro)
					<li class="bg-teal-50 border border-teal-100 rounded-lg p-2 flex items-center justify-between">
						<span class="text-teal-700 font-medium">{{ $seguro->nome_seguro }}</span>
						<span class="text-gray-500 text-xs">{{ $seguro->cobertura }}</span>
					</li>
				@endforeach
			</ul>
		@else
			<div class="text-gray-400 text-sm">Nenhum seguro adicionado ainda.</div>
		@endif
	</div>
	<div class="mt-3">
		<button 
			class="w-full bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-4 rounded-lg shadow flex items-center justify-center"
			onclick="document.getElementById('insuranceModal').style.display = 'block';"
		>
			<i class="fas fa-plus mr-2"></i> Adicionar Seguro
		</button>
	</div>
</div>
