<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <form action="" id="form" method="post" enctype="multipart/form-data">
        <input type="text" name="nombre" id="nombre">
        <input type="file" name="imagen" id="imagen">
        <button type="submit" id="boton">Enviar</button>
    </form>

<script>

const form = document.getElementById("form");

   form.addEventListener("submit",function(e){
    e.preventDefault();
    const formu = new FormData(form);
    const data = Object.fromEntries(formu);
    formu.append("señor",data)
    console.log(data);
    const response =  fetch("api.php", {
    method: "POST",
    body: JSON.stringify(data),
  }).then(json => json.json()).then(json=> console.log(json));
   });

  

</script>
</body>
</html>