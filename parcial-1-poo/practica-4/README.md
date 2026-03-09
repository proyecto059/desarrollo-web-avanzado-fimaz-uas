# Práctica 4 – Integración POO + Herencia + Validaciones + Excepciones

## Objetivo
Implementar un sistema simple en PHP utilizando Programación Orientada a Objetos que integre:
- Encapsulamiento
- Herencia
- Polimorfismo
- Validación de datos
- Manejo de excepciones con try/catch

El sistema simula la gestión de diferentes tipos de usuarios.

## Clases implementadas

### Usuario (clase base)
Contiene:
- nombre
- correo

Incluye validación del correo usando `filter_var`.  
Si el correo no es válido se lanza una excepción.

### Admin
Extiende de `Usuario`.

Método:
- `getRol()` → retorna **Administrador**

### Alumno
Extiende de `Usuario`.

Atributo adicional:
- matricula

Métodos:
- `getMatricula()`
- `getRol()` → retorna **Alumno**

### Invitado
Extiende de `Usuario`.

Atributo adicional:
- empresa

Métodos:
- `getEmpresa()`
- `getRol()` → retorna **Invitado**

## Funcionamiento

En `index.php` se crean varios usuarios:

- 1 Administrador
- 1 Alumno
- 1 Invitado
- 1 usuario con correo inválido para probar la excepción

Se utiliza `try/catch` para manejar el error sin detener el programa.

Los usuarios válidos se muestran en una **tabla HTML**.
