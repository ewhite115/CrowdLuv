    <form class="cluserinfoform" method="post" action="<?php echo BASE_URL; ?>updateuserinfo.php">
       <table class="cldefaulttable cluserinfoform">
            <tr><th><label for="name">Name</label></th>
                <td>
                    <input type="text" name="name" id="name" value="<?php echo $CL_LOGGEDIN_USER_OBJ['firstname'] . ' ' , $CL_LOGGEDIN_USER_OBJ['lastname'];  ?>">
                </td>
            </tr>
            <tr><th><label for="email">Email</label></th>
                <td>
                    <input type="text" name="email" id="email" value="<?php echo $CL_LOGGEDIN_USER_OBJ['email'];  ?>">
                </td>
            </tr>
            <tr><th><label for="mobile">Mobile</label></th>
                <td>
                    <input type="text" name="mobile" id="mobile" value="<?php echo $CL_LOGGEDIN_USER_OBJ['mobile'];  ?>">
                </td>
            </tr>
            <tr><th><label for="city">Your City</label></th>
                <td><input type="text" name="city" id="city" value="<?php echo $CL_LOGGEDIN_USER_OBJ['location_fbname'];?>" disabled></td>
            </tr>
            <tr><th><label for="birthdate">Birth date</label></th>
                <td>
                    <input type="text" name="birthdate" id="birthdate" disabled value="<?php echo $CL_LOGGEDIN_USER_OBJ['birthdate'];  ?>">
                </td>
            </tr>
           <tr><th><label for="rltsp">Relationship</label></th>
                <td>
                    <input type="text" name="rltsp" id="rltsp" disabled value="<?php echo $CL_LOGGEDIN_USER_OBJ['fb_relationship_status'];  ?>">
                </td>
            </tr>

            <tr style="display: none;">
                <?php // the field named address is used as a spam honeypot ?>
                <?php // it is hidden from users, and it must be left blank ?>
                <th>
                    <label for="address">Address</label>
                </th>
                <td>
                    <input type="text" name="address" id="address">
                    <p>Humans (and frogs): please leave this field blank.</p>
                </td>
            </tr>                   
        </table>
    <input type="submit" value="Confirm">
    </form>