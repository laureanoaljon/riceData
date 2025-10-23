<?php

class Yieldmodel extends CI_Model {
    function get_yield_avg($location_code, $location_type, $year, $ecosystem){
        
        $this->db->select('l.locName AS location_name, k.year AS year, AVG("k"."yieldEst") as value');
        $this->db->from('kpi_pay k');
        $this->db->join('ids_location l', 'k.locCode = l.locCode AND k.locType = l.locType');
        $this->db->join('ids_ecosystem e', 'k.eco = e.eco');
        $this->db->where('k.locCode', $location_code);
        $this->db->where('k.locType', $location_type);
        $this->db->where('year', $year);
        $this->db->where('k.eco', $ecosystem);
        $this->db->order_by('year', 'ASC');
        $this->db->group_by(array("year", "location_name"));
        $query=$this->db->get();
        $data_array = $query->result_array();
        return $data_array[0];
    }

    function checkOrGet_yield_avg_current($location_code, $location_type,$year, $ecosystem, $year_type, $period_unit){
        $this->db->select('l.locName AS location_name, k.year AS year, AVG("k"."yieldEst") as value, k.yearType, k.periodUnit');
        $this->db->from('kpi_pays k');
        $this->db->join('ids_location l', 'k.locCode = l.locCode AND k.locType = l.locType');
        $this->db->join('ids_ecosystem e', 'k.eco = e.eco');
        $this->db->where('k.locCode', $location_code);
        $this->db->where('k.locType', $location_type);
        $this->db->where('year', $year);
        $this->db->where('k.eco', $ecosystem);
        $this->db->where('k.yearType', $year_type);
        $this->db->where('k.periodUnit', $period_unit);
        $this->db->order_by('year', 'ASC');
        $this->db->group_by(array("k.year", "location_name", "k.yearType", "k.periodUnit"));
        $query=$this->db->get();
        return $query->result_array();
    }

    function get_yield_avg_current($location_code, $location_type,$year, $ecosystem){
        
        
        $data_array = $this->checkOrGet_yield_avg_current($location_code, $location_type, $year, $ecosystem, '3', 4);

        if (!empty($data_array)) {
            // $data_array has data
            return $this->get_yield_avg($location_code, $location_type, $year, $ecosystem);
        } else {
            // $data_array is empty
            $data_array = $this->checkOrGet_yield_avg_current($location_code, $location_type, $year, $ecosystem, '3', 3);
             if (!empty($data_array)) {
                // $data_array has data
                return $data_array[0];
            }else{
                $data_array = $this->checkOrGet_yield_avg_current($location_code, $location_type, $year, $ecosystem, '3', 2);
                if (!empty($data_array)) {
                    $data_array = $this->checkOrGet_yield_avg_current($location_code, $location_type, $year, $ecosystem, '2', 1);
                    return $data_array[0];
                }else{
                    $data_array = $this->checkOrGet_yield_avg_current($location_code, $location_type, $year, $ecosystem, '3', 1);
                    if (!empty($data_array)) {
                        // $data_array has data
                        return $data_array[0];
                    }else{
                        return $this->get_yield_avg($location_code, $location_type, $year, $ecosystem);
                    }
                }
            }

        }
        //return $data_array[0];
    }

    function get_yield_quarter($location_code, $location_type, $year, $ecosystem, $year_type){
        $this->db->select('l.locName AS location_name, k.year AS year, k.periodUnit AS period_unit, AVG("k"."yieldEst") as value');
        $this->db->from('kpi_pays k');
        $this->db->join('ids_location l', 'k.locCode = l.locCode AND k.locType = l.locType');
        $this->db->join('ids_ecosystem e', 'k.eco = e.eco');
        $this->db->where('k.locCode', $location_code);
        $this->db->where('k.locType', $location_type);
        $this->db->where('year', $year);
        $this->db->where('k.eco', $ecosystem);
        $this->db->where('k.yearType', $year_type);
        $this->db->order_by('period_unit', 'DESC');
        $this->db->group_by(array("period_unit", "year", "location_name"));
        $query=$this->db->get();
        $data_array = $query->result_array();
        // return $data_array[0];
        return $data_array;
    }

    function get_yield_avgs_geocodes($location_code = null, $location_type,  $year_start, $year_end = NULL, 
                                           $ecosystem, $period = NULL, $year_type = NULL, $order = NULL, $limit = NULL, $region_code = NULL){
        
        
        $this->db->select('l.locName AS location_name, g.geoCode AS map_ID, k.year AS year, AVG("k"."yieldEst") as value, k.psgc_code AS psgcCode');
        if($year_type == 1){
            $this->db->from('kpi_pay k');
        } else {
            $this->db->from('kpi_pays k');
        }
        $this->db->join('ids_location l', 'k.locCode = l.locCode AND k.locType = l.locType');
        $this->db->join('ids_geocodes g', 'k.locCode = g.locCode AND k.locType = g.locType');
        $this->db->join('ids_ecosystem e', 'k.eco = e.eco');

        if(isset($location_code)){
            $this->db->where('k.locCode', $location_code);
            $this->db->where('k.locType', $location_type);
            $this->db->order_by('k.year', 'ASC');
        }
        else {
            if($location_type == 1){
                $this->db->where('l.parent', $region_code); 
            }
            if($location_type == 2){
                $this->db->where('k.locCode !=', '999');
				$this->db->where('k.locType', $location_type);				
            }
        }
        
        if(isset($year_end)){
            $this->db->where('k.year >=', $year_start);
            $this->db->where('k.year <=', $year_end);
        }
        else {
            $this->db->where('k.year', $year_start);
        }

        // FOR ECOSYSTEM AND PERIOD FILTER
        if($year_type != 1){
            $this->db->where('k.yearType', $year_type);
            $this->db->where('k.periodUnit', $period);
        }
        
        $this->db->group_by(array("year", "location_name", "geoCode", "psgcCode"));  
        
        if(isset($order)){
            $this->db->order_by('value', $order);
        }
        
        if(isset($limit)){
            $this->db->limit($limit);
        }

        $this->db->where('k.eco', $ecosystem);        
        $query=$this->db->get();
        return $query->result_array();
        
    }

    

     function get_yield_avgs_geocodes_annual($location_code, $location_type, $year, $ecosystem, $region_code = NULL){
        
        
         $this->db->select('l.locName AS location_name, g.geoCode AS map_ID, k.year AS year, AVG("k"."yieldEst") as value');
        $this->db->from('kpi_pay k');
        $this->db->join('ids_location l', 'k.locCode = l.locCode AND k.locType = l.locType');
        $this->db->join('ids_geocodes g', 'k.locCode = g.locCode AND k.locType = g.locType');
        $this->db->join('ids_ecosystem e', 'k.eco = e.eco');
        

        if(isset($location_code)){
            $this->db->where('k.locCode', $location_code);
            $this->db->where('k.locType', $location_type);
        }
        else {
            if($location_type == 1){
                $this->db->where('l.parent', $region_code); 
            }
            if($location_type == 2){
                $this->db->where('k.locCode !=', '999');
				$this->db->where('k.locType', $location_type);				
            }
        }

        $this->db->where('year', $year);
        $this->db->where('k.eco', $ecosystem);
        $this->db->order_by('year', 'ASC');
        $this->db->group_by(array("year", "location_name", "geoCode"));
        $query=$this->db->get();
        $data_array = $query->result_array();
        return $data_array;
    }


     function checkOrGet_yield_avgs_geocodes_current($location_code, $location_type,$year, $ecosystem, $year_type, $period_unit, $region_code = NULL){

     
        $this->db->select('l.locName AS location_name, g.geoCode AS map_ID, k.year AS year, AVG("k"."yieldEst") as value, k.yearType, k.periodUnit');
        $this->db->from('kpi_pays k');
        $this->db->join('ids_location l', 'k.locCode = l.locCode AND k.locType = l.locType');
        $this->db->join('ids_geocodes g', 'k.locCode = g.locCode AND k.locType = g.locType');
        $this->db->join('ids_ecosystem e', 'k.eco = e.eco');

        if(isset($location_code)){
            $this->db->where('k.locCode', $location_code);
            $this->db->where('k.locType', $location_type);
        }
        else {
            if($location_type == 1){
                $this->db->where('l.parent', $region_code); 
            }
            if($location_type == 2){
                $this->db->where('k.locCode !=', '999');
				$this->db->where('k.locType', $location_type);				
            }
        }

        $this->db->where('year', $year);
        $this->db->where('k.eco', $ecosystem);
        $this->db->where('k.yearType', $year_type);
        $this->db->where('k.periodUnit', $period_unit);
        $this->db->order_by('year', 'ASC');
        $this->db->group_by(array("k.year", "location_name", "k.yearType", "k.periodUnit", "geoCode"));
        $query=$this->db->get();
        return $query->result_array();
    }

    function get_yield_avgs_geocodes_current($location_code, $location_type,$year, $ecosystem, $region_code = NULL){
        
        
        $data_array = $this->checkOrGet_yield_avgs_geocodes_current($location_code, $location_type, $year, $ecosystem, '3', 4, $region_code);

        if (!empty($data_array)) {
            // $data_array has data
            return $this->get_yield_avgs_geocodes_annual($location_code, $location_type, $year, $ecosystem, $region_code);
        } else {
            // $data_array is empty
            $data_array = $this->checkOrGet_yield_avgs_geocodes_current($location_code, $location_type, $year, $ecosystem, '3', 3, $region_code);
             if (!empty($data_array)) {
                // $data_array has data
                return $data_array;
            }else{
                $data_array = $this->checkOrGet_yield_avgs_geocodes_current($location_code, $location_type, $year, $ecosystem, '3', 2, $region_code);
                if (!empty($data_array)) {
                    $data_array = $this->checkOrGet_yield_avgs_geocodes_current($location_code, $location_type, $year, $ecosystem, '2', 1, $region_code);
                    return $data_array;
                }else{
                    $data_array = $this->checkOrGet_yield_avgs_geocodes_current($location_code, $location_type, $year, $ecosystem, '3', 1, $region_code);
                    if (!empty($data_array)) {
                        // $data_array has data
                        return $data_array;
                    }else{
                        return $this->get_yield_avgs_geocodes_annual($location_code, $location_type, $year, $ecosystem, $region_code);
                    }
                }
            }

        }
       
    }


    function get_yield_avgs_geocodes_municities($location_code, $location_type,  $year_start, $year_end = NULL, 
                                           $ecosystem, $order = NULL, $limit = NULL, $region_code = NULL, $sem_prism = NULL){
        
        $this->db->select('locCode, locType AS regType, locName AS region, sort');
        $this->db->from('ids_location');
        $this->db->where('locType', '1');
        if($region_code != NULL && $region_code != '999'){
            $this->db->where('locCode', $location_code);
        }
        $this->db->order_by('sort', 'ASC'); 
        $subQuery =  $this->db->get_compiled_select();

        $this->db->select('r.locCode, r.region AS locName, l.locName AS prov, l.locCode AS provCode');
        $this->db->from('ids_location l');
        $this->db->join("($subQuery) r", 'r.locCode = l.parent');
        if($region_code == NULL){
            $this->db->where('l.locCode', $location_code);
        }
        $this->db->where('l.locType', '2');
        $locQuery = $this->db->get_compiled_select();     
        
        $this->db->select('m.locCode AS regCode, m.locName AS regName, m.prov AS provName, m.provCode, l.locCode AS munCode, l.locName AS munName');
        $this->db->from('ids_location l');
        $this->db->join("($locQuery) m", 'm.provCode = l.parent');
        $this->db->where('l.locType', '3');
        $munQuery = $this->db->get_compiled_select();     
        
        $this->db->select('l.munName AS location_name, g.geoCode AS map_ID, k.year AS year, k.yieldEst as value');
        $this->db->select("concat(\"munName\",'\\n',\"provName\") AS location_name, g.geoCode AS map_ID, k.yieldEst as value");
        $this->db->from("($munQuery) l");
        $this->db->join('kpi_pay_prism k', 'l.munCode = k.locCode AND l.provCode = k.provinceCode', 'left outer');
        $this->db->join('ids_geocodes g', 'l.munCode = g.locCode AND l.provCode = g.parent');
        $this->db->join('ids_ecosystem e', 'k.eco = e.eco'); 
        $this->db->where('k.sem', $sem_prism);
        $this->db->where('k.locType', '3');
        $this->db->where('g.locType', '3');
        $this->db->where('k.yieldEst >', '0');
        $this->db->order_by('value', $order);

        if(isset($year_end)){
            $this->db->where('k.year >=', $year_start);
            $this->db->where('k.year <=', $year_end);
        }
        else {
            $this->db->where('k.year', $year_start);
        }
        
        if(isset($ecosystem)){
            $this->db->where('k.eco <=', $ecosystem);
        }

        $query=$this->db->get();
        return $query->result_array();
        
    }


    function get_prism_data($location_code = NULL, $location_type, $year, $ecosystem, $sem) {        
        // Select specific columns
        $this->db->select('l.locName AS location_name, k.year AS year, k.yieldEst AS value');
        $this->db->from('kpi_pay_prism k');
        $this->db->join('ids_location l', 'k.locCode = l.locCode AND k.locType = l.locType AND k.provinceCode = l.parent');
        $this->db->join('ids_ecosystem e', 'k.eco = e.eco');
        $this->db->where('k.locType', $location_type);
        $this->db->where('k.year', $year);
        $this->db->where('k.eco', $ecosystem);
        $this->db->where('k.sem', $sem);
        
        if ($location_code !== NULL) {
            $this->db->where('k.locCode', $location_code);
        }
    
        // Exclude rows where k.yieldEst is NULL
        $this->db->where('k.yieldEst IS NOT NULL');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    
    
    function get_yield_area_geocodes($location_code = null, $location_type,  $year_start, $year_end = NULL, 
                                        $ecosystem, $period = NULL, $year_type = NULL, $order = NULL, $limit = NULL, $region_code = NULL){

        $this->db->select('l.locName AS location_name, g.geoCode AS map_ID, k.year AS year, AVG("k"."yieldEst") as value_yield, SUM("k"."areaHarv") as value_area');
        if($year_type == 1){
        $this->db->from('kpi_pay k');
        } else {
        $this->db->from('kpi_pays k');
        }
        $this->db->join('ids_location l', 'k.locCode = l.locCode AND k.locType = l.locType');
        $this->db->join('ids_geocodes g', 'k.locCode = g.locCode AND k.locType = g.locType');
        $this->db->join('ids_ecosystem e', 'k.eco = e.eco');

        if(isset($location_code)){
        $this->db->where('k.locCode', $location_code);
        $this->db->where('k.locType', $location_type);
        $this->db->order_by('k.year', 'ASC');
        }
        else {
        if($location_type == 1){
        $this->db->where('l.parent', $region_code); 
        }
        if($location_type == 2){
        $this->db->where('k.locCode !=', '999');
        $this->db->where('k.locType', $location_type);				
        }
        }

        if(isset($year_end)){
        $this->db->where('k.year >=', $year_start);
        $this->db->where('k.year <=', $year_end);
        }
        else {
        $this->db->where('k.year', $year_start);
        }

        // FOR ECOSYSTEM AND PERIOD FILTER
        if($year_type != 1){
        $this->db->where('k.yearType', $year_type);
        $this->db->where('k.periodUnit', $period);
        }

        $this->db->group_by(array("year", "location_name", "geoCode"));  

        if(isset($order)){
        $this->db->order_by('value_yield', $order);
        }

        if(isset($limit)){
        $this->db->limit($limit);
        }

        $this->db->where('k.eco', $ecosystem);        
        $query=$this->db->get();
        return $query->result_array();

    }
    
}
?>


