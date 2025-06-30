<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Seguro de Viagem</title>

    <!-- Bootstrap CSS direto no head -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome para os ícones -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
      .box-white {
        background-color: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
        margin-bottom: 2rem;
      }

      .box-white-content {
        padding: 1.5rem;
      }

      .box-white h2 {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 1rem;
      }

      .benefits-list {
        list-style: none;
        padding: 0;
      }

      .benefits-list li {
        position: relative;
        padding-left: 1.5rem;
        margin-bottom: 0.75rem;
        font-size: 1rem;
        line-height: 1.4;
        color: #1a202c;
      }

      .benefits-list li::before {
        content: "✔";
        position: absolute;
        left: 0;
        top: 0;
        color: #20c997;
        font-weight: bold;
      }

      .description p {
        color: #4a5568;
        margin-bottom: 1rem;
        font-size: 1rem;
        line-height: 1.6;
      }
    </style>
</head>
<body class="bg-light">

<div class="container my-5">

  <div class="box-white">
    <div class="box-white-content">
      <h2>Seguros</h2>
      @if(count($frases) > 0)
        <ul class="benefits-list">
          @foreach($frases as $frase)
            <li>{{ $frase }}</li>
          @endforeach
        </ul>
      @else
        <p>Nenhuma frase foi encontrada.</p>
      @endif
    </div>
  </div>

  <!--<div class="box-white">
    <div class="box-white-content description">
      <h2>Descrição Detalhada</h2>
      @foreach(explode("\n\n", $description) as $paragraph)
        <p>{{ $paragraph }}</p>
      @endforeach
    </div>
  </div>-->

  <div class="text-end mt-4">
    <a href="{{ route('trip.form.step5.view') }}" class="btn btn-primary">Continuar</a>
  </div>

</div>

</body>
</html>
