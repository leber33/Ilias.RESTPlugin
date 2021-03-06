<?php
/*
 * Prototypical implementation of some rest endpoints for development
 * and testing.
 */

$app->group('/admin', function () use ($app) {

    /**
     * this is a tool for developers / admins to get
     * fast descriptions of objects or users specified by
     * obj_id, ref_id, usr_id or file_id
     *
     * Supported types: obj_id, ref_id, usr_id and file_id
     */
    $app->get('/describe/:id', 'authenticateILIASAdminRole', function ($id) use ($app) {
        $request = new ilRestRequest($app);
        $response = new ilRestResponse($app);

        try {
            $id_type = $request->getParam('id_type');
        } catch (Exception $e) {
            $id_type = 'ref_id';
        }

        $model = new ilDescribrModel();
        if ($id_type == 'ref_id' || $id_type == 'obj_id') {
            if ($id_type == 'ref_id') {
                $obj_id = ilRestLib::refid_to_objid($id);
                $id_type = 'obj_id';
            }
            //echo "obj_id:".$obj_id;
            try {
                if (is_numeric($obj_id) == false) {
                    throw new Exception('Obj id does not exist');
                }
                $a_descr = $model->describeIliasObject($obj_id);
                $response->addData('object_description', $a_descr);
                $response->setMessage('Object found.');

                if ($a_descr['type'] == "file") {
                    $id = $obj_id;
                    $id_type = "file_id";
                }
            } catch (Exception $e) {
                $response->setRestCode('-11');
                $response->setMessage('Error: Object not found.');
                // Try to explain a user with id = '.$id.' instead.';
                $id_type = 'usr_id';
            }
        }

        if ($id_type == 'usr_id') {
            $username = ilRestLib::userIdtoLogin($id);
            //echo $username;
            try {
                if ($username == 'User unknown') {
                    $response->setMessage('User not found.');
                    throw new Exception('User does not exist');
                } else {
                    ilRestLib::initDefaultRestGlobals();
                    $usr_model = new ilUsersModel();
                    $usr_basic_info =  $usr_model->getBasicUserData($id);
                    if (empty($usr_basic_info) == true) {
                        $response->setMessage('Error: User not found.');
                    } else {
                        $response->setMessage('User  found.');
                        $response->addData('user', $usr_basic_info);
                    }
                }
            } catch (Exception $e) {
                $response->setRestCode('-11');
                $response->setMessage('Error: User not found.');
                // Try to explain a file with id = '.$id.' instead.';
                $id_type = 'file_id';
            }
        }

        if ($id_type == 'file_id') {
            try {
                $data = $model->describeFile($id);
                $response->addMessage('Description of file with id = '.$id.'.');
                $response->addData('file', $data);
            } catch (Exception $e) {
                $response->setRestCode('-11');
                $response->setMessage('Error: File not found.');
            }
        }
        $response->send();
    });

});
