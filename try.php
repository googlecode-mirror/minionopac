<?php
include ('inc/config.php');
//memberid
$memberid = '2';
//usahakan untuk memastikan agar tidak ada term ganda.
//ambil term dari preferensi berdasarkan penanda 'pref'
$term_dr_preferensi = mysql_query("SELECT id,term,collection_id FROM preferensi WHERE collection_id = 'pref' ORDER BY term");
//simpan term dari preferensi ke variable
while ($term_dr_pref = mysql_fetch_array($term_dr_preferensi)){
$term_pref[] = $term_dr_pref['term'];
}
//kalkulasi jumlah term yang ada di preferensi
$count_termpref = mysql_num_rows($term_dr_preferensi);
//echo $count_termpref."<br>";
//daftar collection_id yang menjadi preferensi pengguna
$getcurrent_user_pref = mysql_query("SELECT DISTINCT collection_id FROM user_preference WHERE current = '1' AND user_id = '$memberid'") or die(mysql_error());
while ($fetch_current_user_pref = mysql_fetch_array($getcurrent_user_pref)){
$getpref1[] = $fetch_current_user_pref['collection_id'];
}
//daftar collection_id yang bukan preferensi pengguna
$not_pref_id = mysql_query("SELECT DISTINCT collection_id FROM collection
WHERE collection_id NOT IN (" . implode(',', $getpref1).")
");
while ($fetch_not_pref_id = mysql_fetch_array($not_pref_id)){
$getnotprefid[] = $fetch_not_pref_id['collection_id'];
}
//kalkulasi jumlah collection_id yang bukan pref
$count_notprefid = mysql_num_rows($not_pref_id);
//echo $count_notprefid;


print_r($getnotprefid);
echo "<br>";
print_r($term_pref);

//looping conf. max term current preference
$max_termpref = $count_termpref;
//looping conf. max collection_id not user preference
$max_notprefid = $count_notprefid;
//start loop
$counter = 0;
while ($counter < $max_termpref) {
         $counter1 = 0;
         while ($counter1 < $max_notprefid){
           //printf("<br> term ke- %d <br>",$counter);
           $term_saatini = $term_pref[$counter];
           echo "<br>".$term_saatini."<br>";
           //printf("<br> collection_id ke- %d <br>",$counter1);
           $cid_saatini = $getnotprefid[$counter1];
           echo "<br>".$cid_saatini."<br>";
           //ambil term lain
           $n_getotherterm = mysql_query("SELECT * FROM token WHERE token_word = '$term_saatini' AND collection_id = '$cid_saatini'");
           //cek ketersedian
           if (mysql_num_rows($n_getotherterm) < 1){
             //cari n_condition
             $n_carin_condition = mysql_query("SELECT count(*) token_word FROM 'token' WHERE token_word = '$term_saatini'");
             $fetch_carin_condition = mysql_fetch_array($n_carin_condition);
             $n_condition_saatini = $fetch_carin_condition['token_word'];

             mysql_query("INSERT INTO preferensi (user_id, term_status, term, freq, collection_id, n_total, n_condition)
             VALUES ('$memberid','0','$term_saatini','0','$cid_saatini','$N','$n_condition_saatini')");
           }else{
             $n_fetch_ngot = mysql_fetch_array($n_getotherterm);
             $freq_yangada = $n_fetch_ngot['token_freq'];
             //cari n_condition
             $n_carin_condition = mysql_query("SELECT count(*) token_word FROM 'token' WHERE token_word = '$term_saatini'");
             $fetch_carin_condition = mysql_fetch_array($n_carin_condition);
             $n_condition_saatini = $fetch_carin_condition['token_word'];

             mysql_query("INSERT INTO preferensi (user_id, term_status, term, freq, collection_id, n_total, n_condition)
             VALUES ('$memberid','0','$term_saatini','$freq_yangada','$cid_saatini','$N','$n_condition_saatini');");
           }

           $counter1++;
         }

    $counter++;
}



?>