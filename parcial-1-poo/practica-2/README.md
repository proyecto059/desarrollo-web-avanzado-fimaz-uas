# Práctica 2 – Parcial 1

# Práctica 2: Herencia

## Objetivo

Implementar herencia en PHP mediante la creación de una clase derivada que reutilice los atributos y métodos de una clase base.

## Explicación de la herencia aplicada

Se creó una clase base llamada **Usuario**, la cual contiene los atributos:

- nombre
- correo

y los métodos getters y setters correspondientes.

la clase es reciclada de la practica 1

Posteriormente se creó la clase **Admin**, la cual **extiende la clase Usuario** utilizando la palabra clave `extends`. Gracias a esto, la clase Admin hereda todos los atributos y métodos de Usuario.

Además, se implementó el método `getRol()` que retorna el valor **Administrador**.

## Diferencias entre Usuario y Admin


Usuario; Clase base que define atributos y métodos comunes 
Admin: Clase derivada que hereda de Usuario y agrega el método `getRol()` 

