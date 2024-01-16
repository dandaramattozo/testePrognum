<?php

function calcularAtraso($id_contrato, $dt_calculo) {
    
    $detalhes_parcelas = obterDetalhesParcelas($id_contrato);
    $encargos = obterEncargosContrato($id_contrato);

    $resultados = [
        'id_contrato' => $id_contrato,
        'total_atraso' => 0,
        'detalhes_parcelas' => [],
    ];

    foreach ($detalhes_parcelas as $parcela) {
        $dt_vencimento = $parcela['dt_vencimento'];

         // Calcular atraso em dias
         $dias_atraso = calcularDiasAtraso($dt_calculo, $dt_vencimento);


         
        if ($dias_atraso > 0) {
            $juros_moratorios = calcularJurosMoratorios($encargos['juros_moratorios'], $dias_atraso);
            $juros_remuneratorios = calcularJurosRemuneratorios($encargos['juros_remuneratorios'], $dias_atraso);
            $multa = calcularMulta($encargos['multa'], $dias_atraso);

            $va_atraso = calcularValorAtraso($parcela['va_encargo'], $juros_moratorios, $juros_remuneratorios, $multa);

            $resultados['detalhes_parcelas'][] = [
                'nu_parcela' => $parcela['nu_parcela'],
                'dt_vencimento' => $dt_vencimento,
                'va_atraso' => $va_atraso,
            ];

            $resultados['total_atraso'] += $va_atraso;
        }
    }

    return $resultados;
}

?>