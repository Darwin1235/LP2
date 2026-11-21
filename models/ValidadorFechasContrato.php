<?php

class ValidadorFechasContrato
{
    public static function calcularDias($fechaInicio, $fechaFin)
    {
        $inicio = DateTimeImmutable::createFromFormat('!Y-m-d', $fechaInicio);
        $fin = DateTimeImmutable::createFromFormat('!Y-m-d', $fechaFin);

        if (
            !$inicio ||
            !$fin ||
            $inicio->format('Y-m-d') !== $fechaInicio ||
            $fin->format('Y-m-d') !== $fechaFin
        ) {
            throw new Exception("Las fechas ingresadas no son validas.");
        }

        if ($fin < $inicio) {
            throw new Exception("La fecha de devolucion no puede ser anterior a la fecha de inicio.");
        }

        $dias = $inicio->diff($fin)->days;
        return max(1, $dias);
    }

    /**
     * Evalúa el estado de vencimiento respecto a la fecha actual.
     * Retorna días positivos si aún falta tiempo, 0 si vence hoy, y negativos si ya venció.
     */
    public static function evaluarVencimiento($fechaFinPrevista)
    {
        $hoy = new DateTimeImmutable(date('Y-m-d'));
        $limite = DateTimeImmutable::createFromFormat('!Y-m-d', $fechaFinPrevista);

        if (!$limite) {
            return ['dias_diferencia' => 0, 'vencido' => false, 'dias_atraso' => 0, 'dias_restantes' => 0];
        }

        $diff = $hoy->diff($limite);
        $dias = (int)$diff->format('%r%a'); // negativo si ya pasó, positivo si falta

        return [
            'dias_diferencia' => $dias,
            'vencido' => ($dias < 0),
            'dias_atraso' => ($dias < 0) ? abs($dias) : 0,
            'dias_restantes' => ($dias >= 0) ? $dias : 0
        ];
    }
}
