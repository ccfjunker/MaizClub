<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
	    <h1>QUERO</h1>
		<h2>Link para pagamento</h2>
		<p>Olá {{$p_UserName}}. Siga o link abaixo para completar seu pagamento</p>
		<a href="{{$p_CheckoutURL}}">PagSeguro</a>
		<p>Caso não esteja conseguindo seguir o link acima, copie e cole o endereço abaixo no seu navegador</p>
		<p>{{$p_CheckoutURL}}</p>
	</body>
</html>
