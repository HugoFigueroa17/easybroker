<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    public function index()
    {
        $data['title'] = 'Inicio';

        $datos = $this->getDatosApi(1,40);
        $data['datos'] = $datos;

        //Styles
        $data['styles'][] = base_url('assets/plugins/datatables/DataTables-1.10.21/css/dataTables.bootstrap4.css');
        $data['styles'][] = base_url('assets/plugins/datatables/Buttons-1.6.2/css/buttons.bootstrap4.css');
        $data['styles'][] = base_url('assets/css/tables-custom.css');

        //Scripts
        $data['scripts'][] = base_url('assets/plugins/datatables/jquery.dataTables.min.js');
        $data['scripts'][] = base_url('assets/plugins/datatables/DataTables-1.10.21/js/dataTables.bootstrap4.js');
        $data['scripts'][] = base_url('assets/plugins/datatables/Buttons-1.6.2/js/dataTables.buttons.js');
        $data['scripts'][] = base_url('assets/plugins/datatables/Buttons-1.6.2/js/buttons.bootstrap4.js');
        $data['scripts'][] = base_url('assets/plugins/datatables/JSZip-2.5.0/jszip.js');
        $data['scripts'][] = base_url('assets/plugins/datatables/pdfmake-0.1.36/pdfmake.js');
        $data['scripts'][] = base_url('assets/plugins/datatables/pdfmake-0.1.36/vfs_fonts.js');
        $data['scripts'][] = base_url('assets/plugins/datatables/Buttons-1.6.2/js/buttons.html5.js');
        $data['scripts'][] = base_url('assets/plugins/datatables/Buttons-1.6.2/js/buttons.colVis.js');
        $data['scripts'][] = base_url('assets/js/datos.js');

        echo view('htdocs/header', $data);
        echo view('list', $data);
        echo view('htdocs/footer', $data);

    }

    public function getDatosApi($page = 1,$limit = 50){

        try {


            $curl = curl_init("https://api.stagingeb.com/v1/properties?page=$page&limit=$limit&search%5Bupdated_after%5D=2020-03-01T23%3A26%3A53.402Z&search%5Bupdated_before%5D=2025-03-01T23%3A26%3A53.402Z&search%5Boperation_type%5D=sale&search%5Bmin_price%5D=500000&search%5Bmax_price%5D=3000000&search%5Bmin_bedrooms%5D=1&search%5Bmin_bathrooms%5D=1&search%5Bmin_parking_spaces%5D=1&search%5Bmin_construction_size%5D=100&search%5Bmax_construction_size%5D=1000&search%5Bmin_lot_size%5D=100&search%5Bmax_lot_size%5D=1000&search%5Bstatuses%5D%5B%5D=");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = array(
                "Content-Type: application/json",
                "X-Authorization: l7u502p8v46ba3ppgvj5y2aad50lb9",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $resp = curl_exec($curl);
            curl_close($curl);

            // done
            $response = json_decode($resp, true);

            return $response;
        }
        catch (\Exception $e){
            echo $e->getMessage();
        }
    }//getDatosApi
}
