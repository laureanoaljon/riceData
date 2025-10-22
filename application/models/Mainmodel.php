<?php

class Mainmodel extends CI_Model {

    // public function get_current_ph($category = NULL){
    //     $table = $this->get_table_name_by_category($category);
    
    //     if (!empty($table)) {
    //         $temp_array = $this->mainmodel->get_month_data_ph($table);
    //         $temp_array1 = $this->mainmodel->get_year_data_ph($table);
    
    //         // Extract just the year values from the array
    //         $months = array_column($temp_array, 'month');
    //         $years = array_column($temp_array1, 'year');
    
    //         return [
    //         'month' => $months[0] ?? null,
    //         'year' => $years[0] ?? null
    //     ];
    //     } else {
    //         return ['error' => 'Invalid category'];
    //     }
    // }

    public function get_current_ph($category = NULL){
        $table = $this->get_table_name_by_category($category);
    
        if (!empty($table)) {
            $temp_array = $this->mainmodel->get_current_data_ph($table);
    
            // Extract just the year values from the array
            $months = array_column($temp_array, 'month');
    
            return $months[0];
        } else {
            return ['error' => 'Invalid category'];
        }
    }

    public function get_latest_month_ph($category = NULL){
        $table = $this->get_table_name_by_category($category);
    
        if (!empty($table)) {
            $temp_array = $this->mainmodel->get_month_data_ph($table);
    
            // Extract just the year values from the array
            $months = array_column($temp_array, 'month');
    
            return $months[0];
        } else {
            return ['error' => 'Invalid category'];
        }
    }

    public function get_lastest_year_ph($category = NULL){
        $table = $this->get_table_name_by_category($category);
    
        if (!empty($table)) {
            $temp_array = $this->mainmodel->get_year_data_ph($table);
    
            // Extract just the year values from the array
            $years = array_column($temp_array, 'year');
    
            return $years[0];
        } else {
            return ['error' => 'Invalid category'];
        }
    }

    public function get_lastest_year($category = NULL){
        $table = $this->get_table_name_by_category($category);
    
        if (!empty($table)) {
            $temp_array = $this->mainmodel->get_year_data($table, 'DESC');
    
            // Extract just the year values from the array
            $years = array_column($temp_array, 'year');
    
            return $years[0];
        } else {
            return ['error' => 'Invalid category'];
        }
    }


    public function get_lastest_month_ph($category = NULL){
        $table = $this->get_table_name_by_category($category);
    
        if (!empty($table)) {
            $temp_array = $this->mainmodel->get_month_data_ph($table, 'DESC');
    
            // Extract just the year values from the array
            $months = array_column($temp_array, 'month');
    
            return $months[0];
        } else {
            return ['error' => 'Invalid category'];
        }
    }

    public function get_last_year($category = NULL){
        $table = $this->get_table_name_by_category($category);
    
        if (!empty($table)) {
            $temp_array = $this->mainmodel->get_year_data($table, 'ASC');
    
            // Extract just the year values from the array
            $years = array_column($temp_array, 'year');
    
            return $years[0];
        } else {
            return ['error' => 'Invalid category'];
        }
    }

    public function get_lastest_year_prism() {
        $table = 'kpi_pay_prism';
    
        $this->db->distinct();
        $this->db->select('year');
        $this->db->from($table);
        $this->db->where('locType', '3');
        $this->db->where('sem', '3');
        $this->db->order_by('year', 'DESC'); // Fixed ordering
    
        $query = $this->db->get();
    
        // If no results, return an error
        if ($query->num_rows() == 0) {
            return ['error' => 'No data found'];
        }
    
        // Extract only the most recent year
        $result = $query->result_array();
        
        return $result[0]['year']; // Return only the latest year
    }

    public function get_last_year_prism(){
        $table = 'kpi_pay_prism';
    
        $this->db->distinct();
        $this->db->select('year');
        $this->db->from($table);
        $this->db->where('locType', '3');
        $this->db->where('sem', '3');
        $this->db->order_by('year', 'ASC'); // Fixed ordering
    
        $query = $this->db->get();
    
        // If no results, return an error
        if ($query->num_rows() == 0) {
            return ['error' => 'No data found'];
        }
    
        // Extract only the most recent year
        $result = $query->result_array();
        
        return $result[0]['year']; // Return only the latest year
    }

    public function get_available_year(){

        $category = $this->input->post('category');
        
        $table = $this->get_table_name_by_category($category);
    
        if (!empty($table)) {
            $temp_array = $this->mainmodel->get_year_data($table, 'DESC');

            // Extract just the year values from the array
            $years = array_column($temp_array, 'year');
    
            // Return years as JSON
            $data['years'] = $years;
            echo json_encode($data);
        } else {
            // Return an error if the table is not set
            echo json_encode(['error' => 'Invalid category']);
        }
    }

    private function get_table_name_by_category($category) {
        switch ($category) {
            case 'palay_production':
            case 'area_harvested':
            case 'average_yield':
            case 'yield_area_category':
            case 'pay':
                return 'kpi_pay';
            case 'yield_cost_performance':
            case 'profitability':
            case 'productivity':
                return 'rb_ycost';
            case 'pays':
                return 'kpi_pays';
            case 'net_returns':
            case 'production_costs':
            case 'gross_returns':
            case 'costs_and_returns':
                return 'kpi_creturns';
            case 'valuations':
                return 'kpi_value';
            case 'farmgate_price':
            case 'milled_rice_price':
            case 'rice_prices':
                return 'kpi_prices';
            case 'rice_pricess':
                return 'kpi_pricess';
            case 'production_cost':
                return 'kpi_prodcost';
            case 'rice_imports':
            case 'supply_and_utilization':
            case 'supply':
                return 'kpi_sua';
                // return 'kpi_imports';
            case 'rice_importsc':
                return 'kpi_importsc';
            case 'fertilizer_prices':
                return 'kpi_fertprices';
            case 'rice_stocks':
                return 'kpi_ricestocks';
            case 'land_preparation':
            case 'water_management':
            case 'awareness':
            case 'harvesting':
            case 'other_technologies':
                return 'rb_awareness';
            case 'machineries':
                return 'rb_machine';
            case 'crop_establishment':
                return 'rb_cropest';
            case 'nutrient_management':
                return 'rb_fertilizer';
            case 'fertilizer_use':
                return 'rb_fertuse';
            case 'pest_management':
                return 'rb_pest';
            case 'pest_practices':
                return 'rb_pestprac';
            case 'seeds_and_varieties':
            case 'seeds_class':
                return 'rb_seedclass';
            case 'seeding_rate':
                return 'rb_seedrate';
            case 'farmer_demographics':
            case 'farmer_education_and_experience':
            case 'farmer_organizations':
            case 'farmer_trainings':
                return 'rb_socio';
            case 'household_income':
                return 'rb_income';
            case 'farm_characteristics':
                return 'rb_farm';
            case 'poverty_threshold':
                return 'rb_pthreshold';
            case 'prism':
                return 'kpi_pay_prism';
            case 'source_of_income':
                return 'rb_incomesources';
            case 'digital_proficiency':
                return 'rb_digital';
            case 'fimports':
                return 'kpi_fimports';
            case 'wfertprices':
                return 'kpi_wfertprices';
            case 'fertpricesm':
                return 'kpi_fertpricesm';
            case 'rice_inflation':
                return 'kpi_inflation';
            case 'farmer_population':
                return 'kpi_ffrs';
            default:
                return null;
        }
    }

    public function search_by_keywords(){
        $search_text = $this->input->post('search_text');
        $location_code = $this->input->post('location_code');
        $location_type = $this->input->post('location_type');
        
        ///////////////////////////////////////////////////////////////////////
        //maps section provincial
        $temp_array = $this->mainmodel->search_data_by_keywords($search_text);
        
        foreach ($temp_array as $temp) { 
           $category = $temp['page_value'];

            // Get the latest year for the category
            $latest_year = $this->get_lastest_year($category);
    
            // Get the table name for the category
            $table = $this->get_table_name_by_category($category);
    
            // Fetch the data and encode as JSON
            $temp_array = $this->mainmodel->search_data_by_location($location_code, $location_type, $latest_year, $table);
            $data[$category] = json_encode($temp_array);

        }
        
        // $data['results'] = json_encode($temp_array);
        // $temp_array = array();

        echo json_encode($data);
    }

    public function search_by_location() {
        $location_code = $this->input->post('location_code');
        $location_type = $this->input->post('location_type');
    
        // Define the categories to fetch data for
        $categories = [
            'palay_production',
            'area_harvested',
            'average_yield',
            'costs_and_returns',
            'valuations',
            'fertilizer_prices',
            'rice_prices',
            'rice_inflation',
            'rice_imports',
            'supply_and_utilization',
            'rice_stocks',
            'seeds_and_varieties',
            'land_preparation',
            'crop_establishment',
            'nutrient_management',
            'water_management',
            'pest_management',
            'harvesting',
            'machineries',
            'other_technologies',
            'farmer_demographics',
            'household_income',
            'farm_characteristics',
            'farmer_education_and_experience',
            'farmer_organizations',
            'farmer_trainings',
            'yield_area_category',
            'yield_cost_performance',
            'source_of_incomce',
            'productivity',
            'profitability',
            'farmer_population'
        ];
    
        $data = [];
    
        foreach ($categories as $category) {
            // Get the latest year for the category
            $latest_year = $this->get_lastest_year($category);
    
            // Get the table name for the category
            $table = $this->get_table_name_by_category($category);
    
            // Fetch the data and encode as JSON
            $temp_array = $this->mainmodel->search_data_by_location($location_code, $location_type, $latest_year, $table);
            $data[$category] = json_encode($temp_array);
        }
    
        echo json_encode($data);
    }

    // function search_data($search_text) {
    //     // Convert search text to lowercase
    //     $search_text = strtolower($search_text);
    
    //     // Ensure search_text is properly escaped to prevent SQL injection
    //     $this->db->distinct();
    //     $this->db->select('page_value');
    //     $this->db->from('test');
    
    //     // Use case-insensitive LIKE query (converting search text to lowercase)
    //     $this->db->where("LOWER(key_words) LIKE '%" . $this->db->escape_like_str($search_text) . "%'");
    
    //     // Execute the query and return the result
    //     $query = $this->db->get();
    
    //     return $query->result_array();
    // }

    // function search_data_by_keywords($search_text) {
    //     // Convert search text to lowercase
    //     $search_text = strtolower($search_text);
    
    //     // Split the search text into an array of keywords (assuming space as separator)
    //     $keywords = explode(" ", $search_text);
    
    //     // Initialize an empty array to store LIKE conditions
    //     $like_conditions = [];
    
    //     // Loop through each keyword and create a LIKE condition for it
    //     foreach ($keywords as $keyword) {
    //         // Add the LIKE condition for each keyword (case-insensitive)
    //         $like_conditions[] = "LOWER(key_words) LIKE '%" . $this->db->escape_like_str($keyword) . "%'";
    //     }
    
    //     // Ensure search_text is properly escaped to prevent SQL injection
    //     $this->db->distinct();
    //     $this->db->select('page_value');
    //     $this->db->from('sys_searches');
    
    //     // Combine all LIKE conditions with OR (to match any of the keywords)
    //     if (count($like_conditions) > 0) {
    //         $this->db->where("(" . implode(" OR ", $like_conditions) . ")");
    //     }
    
    //     // Execute the query and return the result
    //     $query = $this->db->get();
        
    //     return $query->result_array();
    // }

    // function search_data_by_keywords($search_text) {
    //     // Convert the search text to lowercase for case-insensitive search
    //     $search_text = strtolower($search_text);
    
    //     // Ensure search_text is properly escaped to prevent SQL injection
    //     $search_text = $this->db->escape_like_str($search_text);
    
    //     // Perform a case-insensitive search for the exact word in the 'key_words' column
    //     $this->db->distinct();
    //     $this->db->select('page_value');
    //     $this->db->from('sys_searches');
    
    //     // Add the LIKE condition to search for the exact word (or phrase)
    //     $this->db->where("LOWER(key_words) LIKE '%" . $search_text . "%'");
    
    //     // Execute the query and return the result
    //     $query = $this->db->get();
    
    //     return $query->result_array();
    // }    

    // function search_data_by_keywords($search_text) {
    //     // Convert the search text to lowercase for case-insensitive search
    //     $search_text = strtolower($search_text);
    
    //     // Ensure search_text is properly escaped to prevent SQL injection
    //     $search_text = $this->db->escape_str($search_text);
    
    //     // Perform an exact match search for the word in the 'key_words' column
    //     $this->db->distinct();
    //     $this->db->select('page_value');
    //     $this->db->from('sys_searches');
    
    //     // Add the condition to match the exact word (case-insensitive)
    //     $this->db->where("LOWER(key_words) = '$search_text'");
    
    //     // Execute the query and return the result
    //     $query = $this->db->get();
    //     $results = $query->result_array();

    //     print_r($results);
    
    //     return $results;
    // }

    function search_data_by_keywords($search_text) {
        // Convert search text to lowercase for case-insensitive search
        $search_text = strtolower($search_text);
    
        // Escape the input to prevent SQL injection
        $search_text = $this->db->escape_str($search_text);
    
        // Fetch rows from the database
        $this->db->select('page_value, key_words');
        $this->db->from('sys_searches');
        $query = $this->db->get();
    
        // Initialize an array to store results
        $results = [];
    
        // Use an associative array to track distinct page values
        $distinct_page_values = [];
    
        // Loop through the results and decode JSON data
        foreach ($query->result_array() as $row) {
            // Decode the JSON data in the 'key_words' column
            $key_words = json_decode($row['key_words'], true); // Decode as an associative array
    
            // Check if the search text is found in the decoded array
            foreach ($key_words as $word) {
                if (strtolower($word) === $search_text) {
                    // If the page value is not already in the results, add it
                    if (!isset($distinct_page_values[$row['page_value']])) {
                        $results[] = ['page_value' => $row['page_value']];
                        $distinct_page_values[$row['page_value']] = true; // Mark as added
                    }
                    break; // No need to continue once a match is found for this row
                }
            }
        }
    
        return $results; // Return the response as an array
    }
    

    function search_data_by_location($location_code, $location_type, $year, $table) {    
        // Ensure search_text is properly escaped to prevent SQL injection
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where('locType', $location_type);
        $this->db->where('locCode', $location_code); 
        $this->db->where('year', $year);
    
        // Execute the query and return the result
        $query = $this->db->get();
        
        return $query->result_array();
    }

    function is_location_exists ($location_code, $location_type, $table) {
        if(is_numeric($location_code)){
            $this->db->where('locCode', $location_code);
            $this->db->where('locType', $location_type);
            $query = $this->db->get($table);
            
            if ($query->num_rows() > 0){
               	return true;
            }
            else{
               	return false;
            }
        }
        else {
            return false;
        }
    }

    function get_metadata_details($metadataIdsArray) {
        if (empty($metadataIdsArray)) {
            return [];
        }

        $this->db->select('metadataTitle as title, metadataText as text');
        $this->db->from("ids_metadata");
        $this->db->where_in('metadataCode', $metadataIdsArray);
        $query=$this->db->get();

        $data_array = $query->result_array();
        return $data_array;
    }

    function get_month_data_ph($table){
        $this->db->distinct();
        $this->db->select('month');
        $this->db->from($table);
    
        $this->db->where('locType', '2');
        $this->db->where('locCode', '999'); 
        
        $this->db->order_by('month', 'DESC');
        $query = $this->db->get();

        // Return the result as an associative array
        return $query->result_array();
    }
    
    function get_year_data_ph($table){
        $this->db->distinct();
        $this->db->select('year');
        $this->db->from($table);
    
        $this->db->where('locType', '2');
        $this->db->where('locCode', '999'); 
        
        $this->db->order_by('year', 'DESC');
        $query = $this->db->get();

        // Return the result as an associative array
        return $query->result_array();
    }

    function get_current_data_ph($table) {
    $this->db->distinct();
    $this->db->select('month, year'); // Correct way to select multiple columns
    $this->db->from($table);

    $this->db->where('locType', '2');
    $this->db->where('locCode', '999'); 
    
    $this->db->order_by('year', 'DESC');
    $this->db->order_by('month', 'DESC');
    
    $query = $this->db->get();

    // Return the result as an associative array
    return $query->result_array();
}


    function get_year_data($table, $order = NULL){
        $this->db->distinct();
        $this->db->select('year');
        $this->db->from($table);
        
        // Check tables na walang regional data at maps
        if ($table != 'kpi_sua' && $table != 'rb_socio'){
            if ($table == 'kpi_ffrs' || $table == 'kpi_fertprices' || $table == 'kpi_imports' || $table == 'kpi_importsc' || $table == 'kpi_ricestocks' || $table == 'rb_awareness' || $table == 'rb_cropest' || $table == 'rb_fertilizer' || $table == 'rb_pest' || $table == 'rb_machine' || $table == 'rb_seedclass' || $table == 'rb_seedrate' || $table == 'rb_variety' || $table == 'rb_income'|| $table == 'rb_ycost' || $table == 'kpi_fimports' || $table == 'kpi_wfertprices'){
                $this->db->where('locType', '2');
            } else if ($table == 'kpi_pay_prism'){
                $this->db->where('locType', '3');
            } else {
                $this->db->where('locType', '1');
            }
        }
        
        if(isset($order)){
            $this->db->order_by('year', $order);
        }
        $query = $this->db->get();

        // Return the result as an associative array
        return $query->result_array();
    }

    function get_sqm_data($table, $year, $year_type, $order = NULL){
        $this->db->distinct();
        $this->db->select('periodUnit');
        $this->db->from($table);
        
        $this->db->where('year', $year);
        $this->db->where('yearType', $year_type);
        
        if(isset($order)){
            $this->db->order_by('periodUnit', $order);
        }
        $query = $this->db->get();

        // Return the result as an associative array
        return $query->result_array();
    }

    function get_month_data($table, $year, $month, $order = NULL){
        $this->db->distinct();
        $this->db->select('month');
        $this->db->from($table);
        
        $this->db->where('year', $year);
        $this->db->where('month', $month);
        
        if(isset($order)){
            $this->db->order_by('month', $order);
        }
        $query = $this->db->get();

        // Return the result as an associative array
        return $query->result_array();
    }

    function get_all_regions($table) {
        
        // Subquery
        $this->db->select('locCode, locType');
        $this->db->from($table);
        $this->db->where('locType', '1');
        $this->db->group_by(array("locCode", "locType")); 
        $subQuery =  $this->db->get_compiled_select();
        
        $this->db->select('l.locCode as id, l.locName AS location_name, l.locType AS loc_type');
        $this->db->from("ids_location l");
        $this->db->join("($subQuery) t", 't.locCode = l.locCode AND t.locType = l.locType');
        $this->db->order_by('l.sort', 'ASC');
        $query=$this->db->get();
        $data_array = $query->result_array();
        return $data_array;
    
    }
    
     function get_all_provinces($table, $year_end = NULL) {
        
        // Subquery
        $this->db->select('locCode, locType');
        $this->db->from($table);
        $this->db->where('locType', '2');
        if(isset($year_end)){
            $this->db->where('year', $year_end);
        }
        $this->db->group_by(array("locCode", "locType"));
	    // $this->db->where('locCode !=', "36");
        $subQuery =  $this->db->get_compiled_select();
         
        $this->db->select('l.locCode as id, l.locName AS location_name, l.parent AS region, t.locType');
        $this->db->from("ids_location l");
        $this->db->join("($subQuery) t", 't.locCode = l.locCode AND t.locType = l.locType');
        $subQuery2 =  $this->db->get_compiled_select();
         
        $this->db->select('l.locCode as region_id, l.locName AS region_name, t.id AS province_id, t.location_name as province, t.locType as loc_type');
        $this->db->from("ids_location l");
        $this->db->join("($subQuery2) t", 't.region = l.locCode');
        
        $this->db->order_by('l.sort', 'ASC');  
        $this->db->order_by('t.location_name', 'ASC'); 
        $this->db->where('l.locType', '1');
        $query=$this->db->get();
        $data_array = $query->result_array();
        return $data_array;
       
    }

    function get_all_regions_and_provinces($table, $year_end = NULL) {
        // Fetch regions and provinces
        $this->db->select('l.locCode as id, l.locName AS location_name, l.locType as loc_type, l.parent AS region, l.latitude AS lat, l.longitude AS lon');
        $this->db->from("ids_location l");
        $this->db->where_in('l.locType', ['1', '2']); // Fetch both regions and provinces
        
        // Exclude specific locCodes
        $this->db->where_not_in('l.locCode', ["36"]); 

        // Check that latitude is not null
        $this->db->where('l.latitude IS NOT NULL');

        $this->db->order_by('l.locType', 'ASC'); // Sort by loc_type in ascending order
        $this->db->order_by('l.sort', 'ASC'); // Sort by loc_type in ascending order
        $this->db->order_by('l.parent', 'ASC');

        $query = $this->db->get();
        $locations = $query->result_array();
    
        return $locations; // Return as an indexed array
    }
    

    function get_selected_provinces($region) {
        // Subquery
        $this->db->select('locCode, locType');
        $this->db->from("ids_location");
        $this->db->where('locType', '2');
        $this->db->group_by(array("locCode", "locType"));
	    // $this->db->where('locCode !=', "36");
        $subQuery =  $this->db->get_compiled_select();
         
        $this->db->select('l.locCode as id, l.locName AS location_name, l.parent AS region');
        $this->db->from("ids_location l");
        $this->db->join("($subQuery) t", 't.locCode = l.locCode AND t.locType = l.locType');
        $subQuery2 =  $this->db->get_compiled_select();
         
        $this->db->select('l.locCode as region_id, l.locName AS region_name, t.id AS province_id, t.location_name as province');
        $this->db->from("ids_location l");
        $this->db->join("($subQuery2) t", 't.region = l.locCode');
        
        $this->db->order_by('l.sort', 'ASC');  
        $this->db->order_by('t.location_name', 'ASC'); 
        $this->db->where('l.locType', '1');
        $this->db->where('t.region', $region);
        $query=$this->db->get();
        $data_array = $query->result_array();
        return $data_array;
    }
    
     function get_selected_cities($province) {
        // Subquery
        $this->db->select('locCode, locType');
        $this->db->from("ids_location");
        $this->db->where('locType', '3');
        $this->db->group_by(array("locCode", "locType"));
        $subQuery =  $this->db->get_compiled_select();
         
        $this->db->select('l.locCode as id, l.locName AS location_name, l.parent AS province');
        $this->db->from("ids_location l");
        $this->db->join("($subQuery) t", 't.locCode = l.locCode AND t.locType = l.locType');
        $subQuery2 =  $this->db->get_compiled_select();
         
        $this->db->select('l.locCode as province_id, l.locName AS province, t.id AS city_id, t.location_name as city');
        $this->db->from("ids_location l");
        $this->db->join("($subQuery2) t", 't.province = l.locCode');
        
        $this->db->order_by('l.sort', 'ASC');  
        $this->db->order_by('t.location_name', 'ASC'); 
        $this->db->where('l.locType', '2');
        $this->db->where('t.province', $province);
        $query=$this->db->get();
        $data_array = $query->result_array();
        return $data_array;
    }

    function get_metadata($page) {
        $this->db->distinct();
        $this->db->select('i.metadataTitle as title, i.metadataText as text');
        $this->db->from("sys_metadata s");
        $this->db->join("ids_metadata i", 's.metadataCode = i.metadataCode');
        if(!($page === "all")) {
            $this->db->where('s.uri', $page);
        }
        $this->db->order_by('i.metadataTitle', 'ASC');  
        $query=$this->db->get();
        $data_array = $query->result_array();
        return $data_array;
    }
    
    function get_search_metadata($search) {
        $this->db->distinct();
        $this->db->select('i.metadataTitle as title, i.metadataText as text');
        $this->db->from("sys_metadata s");
        $this->db->join("ids_metadata i", 's.metadataCode = i.metadataCode');
        if(!($search === NULL)) {
            $this->db->like('LOWER("i"."metadataTitle")', strtolower($search), FALSE);
        }
        $this->db->order_by('i.metadataTitle', 'ASC'); 
        $query=$this->db->get();
        $data_array = $query->result_array();
        return $data_array;
    }

    function get_months_by_year($year, $table) {
        $this->db->distinct();
        $this->db->select('k.month');
        // Add a space before 'as k' to properly alias the table
        $this->db->from($table . ' as k');
        $this->db->where('k.year', $year);
        $this->db->order_by('k.month', 'ASC'); // Order by month in descending order 
    
        $query = $this->db->get();
        // Return the result as an associative array
        return $query->result_array();
    }

    function get_inflation_months_by_year($year, $table) {
        $this->db->distinct();
        $this->db->select('k.month');
        // Add a space before 'as k' to properly alias the table
        $this->db->from($table . ' as k');
        $this->db->where('k.year', $year);
        $this->db->where('k.month <>', '13');
        $this->db->order_by('k.month', 'ASC'); // Order by month in descending order 
    
        $query = $this->db->get();
        // Return the result as an associative array
        return $query->result_array();
    }

    function get_ranges_month_year($year, $table, $year_range) {
        $this->db->distinct();
        
        // Correctly select both month and year
        $this->db->select('k.month, k.year');
        
        // Add a space before 'as k' to properly alias the table
        $this->db->from($table . ' as k');
    
        // Ensure $year and $year_range are integers before any arithmetic operation
        $year = (int)trim($year);  // Use trim to remove any potential extra spaces
        $year_range = (int)$year_range;
    
        if ($year_range == 1){
            // Fetch results for a single year
            $this->db->where('k.year', (string)$year);  // Convert back to string for the query
            $this->db->order_by('k.month', 'ASC');
        } else {
            // Handle year range, ensuring correct casting and arithmetic
            $startYear = (string)($year - ($year_range - 1));  // Convert back to string after subtraction
            $endYear = (string)$year;  // Convert year back to string for the query
            
            $this->db->where('k.year >=', $startYear);
            $this->db->where('k.year <=', $endYear);
            $this->db->order_by('k.year', 'ASC');  // Order by year descending first
            $this->db->order_by('k.month', 'ASC');  // Then order by month ascending within each year
        }   
        
        $query = $this->db->get();
        
        // Return the result as an associative array
        return $query->result_array();
    }

    function get_ranges_week_year($year, $table, $year_range) {
        $this->db->distinct();
        
        // Correctly select both month and year
        $this->db->select('k.weekStart, k.weekEnd, k.year');
        
        // Add a space before 'as k' to properly alias the table
        $this->db->from($table . ' as k');
    
        // Ensure $year and $year_range are integers before any arithmetic operation
        $year = (int)trim($year);  // Use trim to remove any potential extra spaces
        $year_range = (int)$year_range;
    
        if ($year_range == 1){
            // Fetch results for a single year
            $this->db->where('k.year', (string)$year);  // Convert back to string for the query
            $this->db->order_by('k.weekEnd', 'ASC');
        } else {
            // Handle year range, ensuring correct casting and arithmetic
            $startYear = (string)($year - ($year_range - 1));  // Convert back to string after subtraction
            $endYear = (string)$year;  // Convert year back to string for the query
            
            $this->db->where('k.year >=', $startYear);
            $this->db->where('k.year <=', $endYear);
            $this->db->order_by('k.year', 'ASC');  // Order by year descending first
            $this->db->order_by('k.weekEnd', 'ASC');  // Then order by month ascending within each year
        }   
        
        $query = $this->db->get();
        
        // Return the result as an associative array
        return $query->result_array();
    }
    
    function get_awareness_technologies($category, $year, $location_code = NULL, $location_type = NULL) {
        $this->db->distinct();
        $this->db->select('t.technology, p.technologyCode AS technology_code, p.rank');
        $this->db->from('rb_awareness p');
        $this->db->join('ids_technology t', 't.technologyCode = p.technologyCode');  
        $this->db->where('p.categoryCode', $category);
        $this->db->where('p.year', $year);
        
        $this->db->where('p.locCode', $location_code);
        $this->db->where('p.locType', $location_type);
        
        $this->db->where('p.percent >', 0); // Ensure percent is greater than 0
        $this->db->order_by('p.rank', 'ASC'); // Order by rank asending
    
        $query = $this->db->get();
        // Return the result as an associative array
        return $query->result_array();
    }

    function get_source_of_power_methods($category, $year, $location_code = NULL) {
        $this->db->distinct();
        $this->db->select("p.srcofpwr as source_of_power, p.rank as rank, 
                            CASE WHEN p.srcofpwr = 'Others' THEN 1 ELSE 0 END AS sort_order");
        $this->db->from('rb_srcofpwr p');
        $this->db->where('p.categoryCode', $category);
        $this->db->where('p.year', $year);
    
        if (isset($location_code)) {
            $this->db->where('p.locCode', $location_code);
        } else {
            $this->db->where('p.locCode', '999');
        }
    
        // Order by the computed column first, then by rank
        $this->db->order_by("sort_order ASC, p.rank ASC");
    
        $query = $this->db->get();
        return $query->result_array();
    }
    


    // function get_source_of_power_methods($category, $year, $location_code = NULL) {
    //     $this->db->distinct();
    //     $this->db->select('p.srcofpwr as source_of_power, p.rank as rank');
    //     $this->db->from('rb_srcofpwr p');
    //     // $this->db->join('ids_technology t', 't.technologyCode = p.technologyCode');  
    //     $this->db->where('p.categoryCode', $category);
    //     $this->db->where('p.year', $year);
        
    //     if (isset($location_code)){
    //         $this->db->where('p.locCode', $location_code);
    //     } else {
    //         $this->db->where('p.locCode', '999');
    //     }
        
    //     $this->db->order_by('p.rank', 'ASC'); // Order by rank asending
    
    //     $query = $this->db->get();
    //     // Return the result as an associative array
    //     return $query->result_array();
    // }

    function get_machineries($year, $location_code = NULL, $location_type = NULL) {
        $this->db->distinct();
        $this->db->select("m.machine, m.rank, CASE WHEN m.machine = 'Others' THEN 1 ELSE 0 END AS sort_order", false);
        $this->db->from('rb_machine m');
        $this->db->where('m.year', $year);
    
        $this->db->where('m.locCode', $location_code);
        $this->db->where('m.locType', $location_type);
      
        $this->db->where('m.percent >', 0);
    
        // Order by sort_order first, then by rank
        $this->db->order_by('sort_order', 'ASC');
        $this->db->order_by('m.rank', 'ASC');
    
        $query = $this->db->get();
        return $query->result_array();
    }
    

    function get_year_by_period($map_boundary, $period, $year_type, $sem_prism = NULL) {
        $this->db->distinct();
        $this->db->select('k.year');
    
        // Determine the table to query from based on boundary and year_type
        if ($map_boundary == "REGION" || $map_boundary == "PROVINCE") {
            if ($year_type == '1') {
                $this->db->from('kpi_pay k'); 
            } else {
                $this->db->from('kpi_pays k'); 
                $this->db->where('k.periodUnit', $period);
                $this->db->where('k.yearType', $year_type);
            }
        } else if ($map_boundary == "MUNICIPALITY") {
            $this->db->from('kpi_pay_prism k');
            $this->db->where('k.sem', $sem_prism);
        }
    
        $this->db->order_by('k.year', 'DESC'); // Order by year ascending
        $this->db->limit(1); // Limit the result to the first row
    
        $query = $this->db->get();
    
        // Return the first year as a single value if found
        $result = $query->row_array();
        return $result ? $result['year'] : null; // Return year or null if no data
    }

    function get_location_by_loc_and_category($category, $location_code, $location_type) {
        
        $this->db->select('*');
    
        if ($category == 'valuations'){
            $this->db->from('kpi_value k'); 
        } else {
            $this->db->from('kpi_pay k');
        }

        $this->db->where('k.locCode', $location_code);
        $this->db->where('k.locType', $location_type);
        $this->db->order_by('k.year', 'DESC'); // Order by year descending
        $this->db->limit(1); // Limit to the latest entry
    
        $query = $this->db->get();
        $result = $query->row_array();

        // Return true if data exists, false otherwise
        return $result ? true : false;
    }

    function get_rb_count($year, $location_code, $location_type) {
        
        $this->db->select('k.count as total_count');
        $this->db->from('rb_socio k'); 
        
        $this->db->where('k.year', $year);
        $this->db->where('k.locCode', $location_code);
        $this->db->where('k.locType', $location_type);
    
        $query = $this->db->get();
        $result = $query->row_array();

        // Return true if data exists, false otherwise
        return $result;
    }

    public function get_current_farmgate_price(){
        // $this->db->distinct;
        $this->db->select('periodUnit as month, year');
        $this->db->from('kpi_pricess');
        $this->db->where('locType', '2');
        $this->db->where('locCode', '999');
        $this->db->where('yearType', '4');
        $this->db->order_by('year', 'DESC');
        $this->db->order_by('month', 'DESC');
        $this->db->limit(1); // get only the latest record

        $query = $this->db->get();
        $result = $query->row_array();

        return $result ? ['month' => $result['month'], 'year' => $result['year']] : null;

    }

    public function get_current_milled_rice_price(){
        // $this->db->distinct;
        $this->db->select('periodUnit as month, year');
        $this->db->from('kpi_pricess');
        $this->db->where('locType', '2');
        $this->db->where('locCode', '999');
        $this->db->where('yearType', '4');
        $this->db->order_by('year', 'DESC');
        $this->db->order_by('month', 'DESC');
        $this->db->limit(1); // get only the latest record

        $query = $this->db->get();
        $result = $query->row_array();

        return $result ? ['month' => $result['month'], 'year' => $result['year']] : null;

    }

    public function get_current_rice_imports(){
        // $this->db->distinct;
        $this->db->select('periodUnit as month, year');
        $this->db->from('kpi_imports');
        $this->db->where('locType', '2');
        $this->db->where('locCode', '999');
        $this->db->where('yearType', '4');
        $this->db->order_by('year', 'DESC');
        $this->db->order_by('month', 'DESC');
        $this->db->limit(1); // get only the latest record

        $query = $this->db->get();
        $result = $query->row_array();

        return $result ? ['month' => $result['month'], 'year' => $result['year']] : null;

    }

    public function get_current_rice_stocks(){
        $this->db->distinct();
        $this->db->select('month, year');
        $this->db->from('kpi_ricestocks');
        $this->db->where('locType', '2');
        $this->db->where('locCode', '999');
        $this->db->order_by('year', 'DESC');
        $this->db->order_by('month', 'DESC');
        $this->db->limit(1); // get only the latest record

        $query = $this->db->get();
        $result = $query->row_array();

        return $result ? ['month' => $result['month'], 'year' => $result['year']] : null;

    }

    public function get_current_rice_inflation(){
        $this->db->distinct();
        $this->db->select('month, year');
        $this->db->from('kpi_inflation');
        $this->db->where('locType', '2');
        $this->db->where('locCode', '999');
        $this->db->where('month !=', '13');
        $this->db->order_by('year', 'DESC');
        $this->db->order_by('month', 'DESC');
        $this->db->limit(1); // get only the latest record

        $query = $this->db->get();
        $result = $query->row_array();

        return $result ? ['month' => $result['month'], 'year' => $result['year']] : null;

    }

    public function get_lastest_year_rice_inflation(){
        $this->db->distinct();
        $this->db->select('year');
        $this->db->from('kpi_inflation');
        $this->db->where('locType', '2');
        $this->db->where('locCode', '999');
        $this->db->where('month', '13');
        $this->db->order_by('year', 'DESC');
        $this->db->limit(1); // get only the latest record

        $query = $this->db->get();
        $result = $query->row_array();

        return $result ? ['year' => $result['year']] : null;

    }

    public function get_fert_prices_annual(){
        $this->db->distinct();
        $this->db->select('year');
        $this->db->from('kpi_fertpricesm');
        $this->db->where('locType', '2');
        $this->db->where('locCode', '999');
        $this->db->where('month', '13');
        $this->db->order_by('year', 'DESC');
        $this->db->limit(1); // get only the latest record

        $query = $this->db->get();
        $result = $query->row_array();

        return $result ? $result['year'] : null;

    }

    // public function get_current_pay_year() {
    //     $this->db->select('year');
    //     $this->db->from('kpi_pay_prism');
    //     $this->db->where('locType', '2');
    //     $this->db->where('locCode', '999');
    //     $this->db->order_by('year', 'DESC');
    //     $this->db->limit(1); // get only the latest record

    //     $query = $this->db->get();
    //     $result = $query->row_array();

    //     return $result ? $result['year'] : null;

    // }


    public function insert_data_to_login_histories($data) {
        return $this->db->insert('users_login_histories_advanced', $data); // Insert data into the 'users' table
    }

    public function get_next_users_id() {
        $this->db->select_max('id');
        $query = $this->db->get('users_login_histories_advanced');
        $result = $query->row();
        return ($result->id ?? 0) + 1;
    }

    public function getClientLocalIP() {
        $ip = $this->input->ip_address();

        // Handle localhost (::1 → 127.0.0.1)
        if ($ip === '::1') {
            $ip = '127.0.0.1';
        }

        // Handle IPv6 mapped IPv4 (::ffff:192.168.x.x)
        if (strpos($ip, '::ffff:') === 0) {
            $ip = substr($ip, 7);
        }

        // // If the IP is private (LAN range), return it
        // if (preg_match('/^(192\.168\.|10\.|172\.(1[6-9]|2[0-9]|3[0-1])\.)/', $ip)) {
        //     return $ip;
        // }

        // Otherwise, it’s a public IP (user not in LAN)
        return $ip;
    }

    public function login_non_philrice_staff($input, $password) {
        $data = array();
    
        // Make sure input is a string
        $input = trim((string) $input);
    
        // Detect input type
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $this->db->where('email', $input);
        } elseif (preg_match('/^\d{2}-\d{4}$/', $input)) {
            $this->db->where('philrice_id', $input);
        } else {
            $this->db->where('name', $input);
        }
    
        $query = $this->db->get('users');
    
        if ($query->num_rows() > 0) {
            $user = $query->row_array();

            // if ($user['allow_access'] != '1') {
            //     $data['message'] = 'Access denied. Please contact administrator to approved your account.';
            //     $data['result'] = array();
            // } else {
                // If you're not using hashed passwords, use direct comparison
                if (password_verify($password, $user['password'])) {
                    $data['message'] = 'Successfully login!';
                    $data['status'] = 'success';
                    $data['result'] = $user;
                } else {
                    $data['message'] = 'Incorrect password!';
                    $data['status'] = 'error';
                    $data['result'] = array();
                }
            // }
            
        } else {
            $data['message'] = 'User not found!';
            $data['result'] = array();
        }
    
        return $data;
    }
    
}
?>