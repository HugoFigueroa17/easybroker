<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div>
                            <table id="tblDatos" class="table m-0 table-bordered dt-responsive cls-table" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th width="5%">Public ID</th>
                                    <th width="10%">Titulo</th>
                                    <th width="8%">Imagen</th>
                                    <th width="20%">Ubicación</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if(isset($datos['content'])){
                                    if(count($datos['content']) > 0){
                                        foreach ($datos['content'] as $d) {
                                            echo "<tr>";
                                            echo '<td>'.$d['public_id'].'</td>';
                                            echo '<td>'.$d['title'].'</td>';
                                            echo '<td><img src="'.$d['title_image_thumb'].'"/></td>';
                                            echo '<td>'.$d['location'].'</td>';
                                            echo "</tr>";
                                        }//foreach
                                    }//if
                                }//if
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>