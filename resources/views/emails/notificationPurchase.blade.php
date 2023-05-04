<?php
    $total = $purchase->amount_in_cents;
    $totalFormat = intval(substr($total, 0, -2));
    $totalFormat = "$". number_format($totalFormat);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra exitosa</title>
</head>
<body>
    <h3>
        Compra exitosa!
        <br>
        REF: {{$purchase->reference}}
    </h3>

    <h4>Informacion de la compra</h4>
    <table border="1">
        <tr>
            <th>Total</th>
            <th>Metodo de pago</th>
            <th>Dirección de envio</th>
            <th>Ciudad</th>
            <th>Departamento</th>
            <th>Telefono</th>
        </tr>
        <tr>
            <td>{{$totalFormat}}</td>
            <td>{{$purchase->payment_method}}</td>
            <td>{{$purchase->address}}</td>
            <td>{{$purchase->city}}</td>
            <td>{{$purchase->region}}</td>
            <td>{{$purchase->phone}}</td>
        </tr>
    </table>

    <h4>Productos comprados</h4>
   
        <table border="1">
            <tr>
                <th>Producto</th>
                <th>Precio</th>
            </tr>
            @foreach($purchase->products as $product)
                <?php
                    $priceFormat = "$". number_format($product->price);
                ?>
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $priceFormat }}</td>
                </tr>
            @endforeach
        </table>
    
</body>
</html>