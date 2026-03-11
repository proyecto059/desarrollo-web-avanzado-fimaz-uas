# Examen Práctico – Parcial 1

# Práctica – Sistema de Usuarios con POO

## Objetivo
Implementar un sistema orientado a objetos en PHP utilizando:
- Encapsulamiento
- Herencia
- Validación de datos
- Manejo de excepciones

## Clases
Usuario: clase base con nombre y correo.
Admin: extiende Usuario y retorna rol Administrador.
Alumno: extiende Usuario y agrega matrícula.

## Funcionamiento
En index.php se crean:
- 1 administrador
- 1 alumno
- 1 usuario con correo inválido para probar la excepción.

Se utiliza try/catch para capturar el error y mostrar un mensaje controlado.
