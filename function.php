<?php 

    function register($data)
    {
        global $conn;

        $nama_lengkap = $data['nama_lengkap'];
        $nik = $data['nik'];
        $email = $data['email'];
        $password = $data['password'];

        $sql = "INSERT INTO users (nama_lengkap, nik, email, password) 
        VALUES(
                '" . $nama_lengkap . "', 
                '" . $nik . "', 
                '" . $email . "', 
                '" . $password . "'
            )";

        if($conn->query($sql)){
            return "Berhasil Mendaftar";
        }else {
            var_dump(mysqli_error($conn));
        };

    }

    function user_update($data)
    {
        global $conn;

        $id = $data['id'];
        $nama_lengkap = $data['nama_lengkap'];
        $nik = $data['nik'];
        $email = $data['email'];
        if (isset($data['new_password'])) {
            $password = $data['new_password'];
            $sql = "UPDATE users SET nama_lengkap = '" . $nama_lengkap . "', nik = '" . $nik . "', email = '" . $email . "', password = '" . $password . "' WHERE id = " . $id;
        }else {
            $sql = "UPDATE users SET nama_lengkap = '" . $nama_lengkap . "', nik = '" . $nik . "', email = '" . $email . "' WHERE id = " . $id;
        }
        
        $result = mysqli_query($conn, $sql);
        if($result){
            return "Data Berhasil Di Update";
        }else {
            var_dump(mysqli_error($conn));
        };

    }

    function is_valid($data){
        $errors = [];
        foreach($data as $key => $dt){
            if ($data[$key] == null || $data[$key] == "") {
                $errors[] = $key . ' tidak boleh kosong';
            }else {
                continue;
            }
        }
        return $errors;
    }

    function login($data)
    {
        global $conn;
        $errors = [];
        $email = $data['email'];
        $password = $data['password'];
        $result = mysqli_query($conn, "SELECT users.*, roles.role_name FROM users JOIN roles ON users.role_id = roles.id WHERE email = '$email'");

        if (mysqli_num_rows($result)) {

            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row['password']) && $row['role_id'] == 1 || $row['role_id'] == 2) {
                if (set_user_session($row['id'])) {
                    echo "<script> alert('loged'); </script>";
                    header("Location: admin/index");
                    $_SESSION['auth'] = $row;
                }
            }elseif(password_verify($password, $row['password']) && $row['role_id'] == 3){
                if (set_user_session($row['id'])) {
                    echo "<script> alert('loged'); </script>";
                    header("Location: masyarakat/index");
                    $_SESSION['auth'] = $row;
                }
            }else{
                return $errors[] = 'Password salah!';
            }

        }else {
            return $errors[] = 'Email anda <b>' . $email . '</b> belum terdaftar';
        }
    }

    function set_user_session($id)
    {
        global $conn;
        $id = $id;
        $ip_address = get_client_ip();
        $result = mysqli_query($conn, "INSERT INTO sessions (user_id, ip_address) VALUES ('$id', '$ip_address')");
        if ($result) {
            return true;
        }

    }

    function get_client_ip() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    function mengadu($data)
    {
        global $conn;
        $id = rand(0000, 9999);
        $user_id = $_SESSION['auth']['id'];
        $judul_aduan = $data['judul_aduan'];
        $isi_aduan = $data['isi_aduan'];
        $foto = $data['foto'];
        
        $sql = "INSERT INTO pengaduan (id, user_id, judul_aduan, isi_aduan, foto) 
        VALUES(
                '" . $id . "', 
                '" . $user_id . "', 
                '" . $judul_aduan . "', 
                '" . $isi_aduan . "',
                '" . $foto . "'
            )";

        if($conn->query($sql)){
            return "Berhasil Mengadu";
        }else {
            var_dump(mysqli_error($conn));
        };

    }