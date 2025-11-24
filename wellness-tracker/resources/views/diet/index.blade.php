@extends('layouts.app_custom')

@section('content')

<!-- HEADER & ACTIONS -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h2>Diario de Comidas</h2>
        <p style="color: var(--text-body);">Registra tu alimentación y controla tus macros</p>
    </div>
    <button onclick="openMealModal()" class="btn-primary" style="border: none; padding: 12px 25px; border-radius: 50px; font-weight: 600; cursor: pointer;">
        + Registrar Comida
    </button>
</div>

<!-- RESUMEN DE HOY (MACROS) -->
<h4 style="margin-bottom: 15px; color: var(--text-head);">Resumen de Hoy</h4>
<div class="grid-4" style="margin-bottom: 40px;">
    <!-- Calorías -->
    <div class="card" style="text-align: center; border-bottom: 4px solid var(--primary);">
        <div style="font-size: 0.9rem; color: #888; margin-bottom: 5px;">Calorías</div>
        <div style="font-size: 1.8rem; font-weight: 700; color: var(--text-head);">
            {{ $todayStats['calories'] }} <span style="font-size: 1rem; font-weight: 400;">kcal</span>
        </div>
    </div>
    
    <!-- Proteína -->
    <div class="card" style="text-align: center; border-bottom: 4px solid #ef4444;">
        <div style="font-size: 0.9rem; color: #888; margin-bottom: 5px;">Proteína</div>
        <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-head);">
            {{ $todayStats['protein'] }}g
        </div>
    </div>

    <!-- Carbs -->
    <div class="card" style="text-align: center; border-bottom: 4px solid #f59e0b;">
        <div style="font-size: 0.9rem; color: #888; margin-bottom: 5px;">Carbohidratos</div>
        <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-head);">
            {{ $todayStats['carbs'] }}g
        </div>
    </div>

    <!-- Grasas -->
    <div class="card" style="text-align: center; border-bottom: 4px solid #3b82f6;">
        <div style="font-size: 0.9rem; color: #888; margin-bottom: 5px;">Grasas</div>
        <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-head);">
            {{ $todayStats['fats'] }}g
        </div>
    </div>
</div>

<!-- HISTORIAL DE COMIDAS -->
<h3 style="margin-bottom: 20px;">Historial Reciente</h3>
<div class="grid-3">
    @forelse($meals as $meal)
        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span style="font-size: 0.8rem; background: #eee; padding: 3px 10px; border-radius: 20px;">
                        {{ $meal->eaten_at->format('d M, H:i') }}
                    </span>
                    
                    <form action="{{ route('diet.destroy', $meal->id) }}" method="POST" onsubmit="return confirm('¿Eliminar comida?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: none; border: none; color: #ccc; cursor: pointer; font-size: 1rem;" title="Eliminar">✕</button>
                    </form>
                </div>

                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                    @if($meal->image_path)
                        <!-- Si hay imagen subida -->
                        <div style="width: 60px; height: 60px; background-image: url('{{ asset('storage/'.$meal->image_path) }}'); background-size: cover; border-radius: 10px;"></div>
                    @else
                        <!-- Icono por defecto -->
                        <div style="width: 60px; height: 60px; background: #dcfce7; color: #22c55e; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 1.5rem;">
                            <i class="bi bi-egg-fried"></i>
                        </div>
                    @endif
                    
                    <div>
                        <h4 style="margin: 0;">{{ $meal->name }}</h4>
                        <div style="font-weight: 700; color: var(--primary);">{{ $meal->calories }} kcal</div>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: #666; background: #f9f9f9; padding: 10px; border-radius: 8px;">
                    <span><strong>P:</strong> {{ $meal->protein_g }}g</span>
                    <span><strong>C:</strong> {{ $meal->carbs_g }}g</span>
                    <span><strong>G:</strong> {{ $meal->fats_g }}g</span>
                </div>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 50px; background: white; border-radius: 20px; border: 2px dashed #ddd;">
            <i class="bi bi-basket" style="font-size: 3rem; color: #ddd;"></i>
            <p>No has registrado comidas hoy.</p>
        </div>
    @endforelse
</div>

<!-- MODAL REGISTRO CON IA -->
<div id="mealModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; width: 90%; max-width: 500px; padding: 30px; border-radius: 20px; position: relative; max-height: 90vh; overflow-y: auto;">
        
        <h3 style="margin-bottom: 20px;">Registrar Alimento</h3>
        
        <!-- SECCIÓN DE ANÁLISIS IA -->
        <div style="background: #f0fdf4; border: 1px dashed #22c55e; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center;">
            <p style="color: #166534; font-weight: 600; margin-bottom: 10px; font-size: 0.9rem;">
                <i class="bi bi-magic"></i> ¿Te da pereza escribir?
            </p>
            <input type="file" id="imageInput" accept="image/*" style="display: none;" onchange="analyzeImage(this)">
            
            <button onclick="document.getElementById('imageInput').click()" style="background: white; border: 1px solid #22c55e; color: #22c55e; padding: 8px 15px; border-radius: 50px; cursor: pointer; font-size: 0.9rem;">
                Subir Foto y Analizar con IA
            </button>

            <!-- Loading Spinner -->
            <div id="loadingAnalysis" style="display: none; margin-top: 15px;">
                <div style="width: 20px; height: 20px; border: 3px solid #ddd; border-top: 3px solid #22c55e; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 5px;"></div>
                <small style="color: #666;">Analizando alimento...</small>
            </div>
        </div>

        <!-- FORMULARIO FINAL -->
        <form action="{{ route('diet.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Input oculto para re-enviar la imagen analizada si se desea guardar -->
            <input type="file" name="image_final" id="finalImageInput" style="display: none;">

            <div style="margin-bottom: 15px;">
                <label class="form-label">Nombre del plato</label>
                <input type="text" name="name" id="foodName" class="form-control" required placeholder="Ej: Pechuga de pollo">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label class="form-label">Calorías</label>
                    <input type="number" name="calories" id="foodCals" class="form-control" required>
                </div>
                <div>
                    <label class="form-label">Fecha/Hora</label>
                    <input type="datetime-local" name="eaten_at" class="form-control" required value="{{ now()->format('Y-m-d\TH:i') }}">
                </div>
            </div>

            <label class="form-label">Macros (gramos)</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 25px;">
                <div>
                    <input type="number" step="0.1" name="protein_g" id="foodProt" class="form-control" placeholder="Prot">
                    <small style="color: #888;">Proteína</small>
                </div>
                <div>
                    <input type="number" step="0.1" name="carbs_g" id="foodCarbs" class="form-control" placeholder="Carbs">
                    <small style="color: #888;">Carbs</small>
                </div>
                <div>
                    <input type="number" step="0.1" name="fats_g" id="foodFats" class="form-control" placeholder="Grasa">
                    <small style="color: #888;">Grasa</small>
                </div>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; padding: 12px; border: none; border-radius: 50px; cursor: pointer; font-weight: 600;">Guardar en Diario</button>
        </form>
        
        <button onclick="document.getElementById('mealModal').style.display='none'" style="position: absolute; top: 15px; right: 15px; border: none; background: none; font-size: 1.2rem; cursor: pointer;">✕</button>
    </div>
</div>

<style>
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    .form-label { display: block; margin-bottom: 5px; font-weight: 500; font-size: 0.9rem; }
    .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; }
</style>

<script>
    function openMealModal() {
        document.getElementById('mealModal').style.display = 'flex';
    }

    // Lógica AJAX para analizar imagen
    async function analyzeImage(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Mostrar loading
            document.getElementById('loadingAnalysis').style.display = 'block';
            
            // Preparar FormData
            const formData = new FormData();
            formData.append('image', file);
            
            // CSRF Token para Laravel
            const token = document.querySelector('input[name="_token"]').value;

            try {
                const response = await fetch("{{ route('diet.analyze') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token },
                    body: formData
                });

                const data = await response.json();

                // Llenar campos automáticamente
                document.getElementById('foodName').value = data.name;
                document.getElementById('foodCals').value = data.calories;
                document.getElementById('foodProt').value = data.protein_g;
                document.getElementById('foodCarbs').value = data.carbs_g;
                document.getElementById('foodFats').value = data.fats_g;
                
                // Pasar el archivo al input final del formulario para guardarlo de verdad si el usuario da Guardar
                // Nota: Copiar archivos entre inputs no es posible por seguridad en browsers, 
                // así que el usuario solo guardará los DATOS numéricos, a menos que suba la foto manual de nuevo.
                // O podemos dejarlo así, la "magia" es obtener los datos.

            } catch (error) {
                alert("Error al analizar la imagen. Intenta de nuevo.");
            } finally {
                document.getElementById('loadingAnalysis').style.display = 'none';
            }
        }
    }
</script>

@endsection