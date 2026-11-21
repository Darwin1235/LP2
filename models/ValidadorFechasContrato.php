
<?php

class ValidadorFechasContrato
{
    public static function calcularDias($fechaInicio, $fechaFin)
    {
        // Verificar que las fechas tengan el formato correcto
        $inicio = DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $fechaInicio
        );

        $fin = DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $fechaFin
        );

        // Comprobar si las fechas son validas
        if (
            !$inicio ||
            !$fin ||
            $inicio->format('Y-m-d') !== $fechaInicio ||
            $fin->format('Y-m-d') !== $fechaFin
        ) {
            throw new Exception(
                "Las fechas ingresadas no son validas."
            );
        }

        // La fecha final no puede ser menor que la inicial
        if ($fin < $inicio) {
            throw new Exception(
                "La fecha de devolucion no puede ser anterior a la fecha de inicio."
            );
        }

        // Calcular los dias del alquiler
        $dias = $inicio->diff($fin)->days;

        // Se cobra como minimo un dia de alquiler
        return max(1, $dias);
    }
}
