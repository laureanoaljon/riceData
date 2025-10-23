<?php

class Areaharvestedmodel extends CI_Model {

    function get_harvestarea_total($location_code, $location_type, $year, $ecosystem){
        
        $this->db->select('l.locName AS location_name, k.year AS year, SUM("k"."areaHarv") as value');
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

    function checkOrGet_harvestarea_total_current($location_code, $location_type,$year, $ecosystem, $year_type, $period_unit){
        $this->db->select('l.locName AS location_name, k.year AS year, SUM("k"."areaHarv") as value, k.yearType, k.periodUnit');
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

    function get_harvestarea_total_current($location_code, $location_type,$year, $ecosystem){
        
        
        $data_array = $this->checkOrGet_harvestarea_total_current($location_code, $location_type, $year, $ecosystem, '3', 4);

        if (!empty($data_array)) {
            // $data_array has data
            return $this->get_harvestarea_total_current($location_code, $location_type, $year, $ecosystem);
        } else {
            // $data_array is empty
            $data_array = $this->checkOrGet_harvestarea_total_current($location_code, $location_type, $year, $ecosystem, '3', 3);
             if (!empty($data_array)) {
                // $data_array has data
                return $data_array[0];
            }else{
                $data_array = $this->checkOrGet_harvestarea_total_current($location_code, $location_type, $year, $ecosystem, '3', 2);
                if (!empty($data_array)) {
                    $data_array = $this->checkOrGet_harvestarea_total_current($location_code, $location_type, $year, $ecosystem, '2', 1);
                    return $data_array[0];
                }else{
                    $data_array = $this->checkOrGet_harvestarea_total_current($location_code, $location_type, $year, $ecosystem, '3', 1);
                    if (!empty($data_array)) {
                        // $data_array has data
                        return $data_array[0];
                    }else{
                        return $this->get_harvestarea_total_current($location_code, $location_type, $year, $ecosystem);
                    }
                }
            }

        }
        //return $data_array[0];
    }


    function get_harvestarea_quarter($location_code, $location_type, $year, $ecosystem, $year_type){
        $this->db->select('l.locName AS location_name, k.year AS year, k.periodUnit AS period_unit, SUM("k"."areaHarv") as value');
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

    function get_harvestarea_totals_geocodes($location_code = null, $location_type,  $year_start, $year_end = NULL, 
                                            $ecosystem, $period = NULL, $year_type = NULL, $order = NULL, $limit = NULL, $region_code = NULL){
        
        
        $this->db->select('l.locName AS location_name, g.geoCode AS map_ID, k.year AS year, SUM("k"."areaHarv") as value, k.psgc_code AS psgcCode');
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

    function get_harvestarea_totals($location_code = null, $location_type,  $year_start, $year_end = NULL, 
                                    $ecosystem, $period = NULL, $year_type = NULL, $order = NULL, $limit = NULL, $region_code = NULL){
        
        // Subquery
        if($location_code == NULL){
            $this->db->select('locCode, locType, locName, sort');
            $this->db->from('ids_location');
            $this->db->where('locType', '1');
            $this->db->order_by('sort', 'ASC'); 
            $subQuery =  $this->db->get_compiled_select();    
        }
        
        if($location_code == NULL){
            $this->db->select('l.locName AS location_name, r.locName as region, k.year AS year, SUM("k"."areaHarv") as value');
        } else{
            $this->db->select('l.locName AS location_name, k.year AS year, SUM("k"."areaHarv") as value');
        }
        
        // CHECKER IF HAS PERION FILTER
        if($year_type == 1){
            $this->db->from('kpi_pay k');
        } else {
            $this->db->from('kpi_pays k');
        }

        $this->db->join('ids_location l', 'k.locCode = l.locCode AND k.locType = l.locType');
        if($location_code == NULL){
            $this->db->join("($subQuery) r", 'r.locCode = l.parent');
            $this->db->where('k.locType', '2');
        }
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
               
        if($location_code == NULL){
            $this->db->group_by(array("year", "location_name", "r.locName"));  
        }
        else {
            $this->db->group_by(array("year", "location_name"));  
        }
        
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

    function get_harvestarea_totals_regions($location_code = null, $location_type,  $year_start, $year_end = NULL, 
                                            $ecosystem, $period = NULL, $year_type = NULL, $order = NULL, $limit = NULL, $region_code = NULL){
        
        // Subquery
        if($location_code == NULL){
            $this->db->select('locCode, locType, locName, sort');
            $this->db->from('ids_location');
            $this->db->where('locType', '1');
            $this->db->order_by('sort', 'ASC'); 
            $subQuery =  $this->db->get_compiled_select();    
        }

        if($location_code == NULL){
            if($location_type == 1){
                $this->db->select('l.locName AS location_name, k.year AS year, SUM("k"."areaHarv") as value');
            }
            if($location_type == 2){
                $this->db->select('l.locName AS location_name, r.locName AS region, k.year AS year, SUM("k"."areaHarv") as value');
            }
        } else{
            $this->db->select('l.locName AS location_name, k.year AS year, SUM("k"."areaHarv") as value');
        }
        
        // CHECKER IF HAS PERION FILTER
        if($year_type == 1){
            $this->db->from('kpi_pay k');
        } else {
            $this->db->from('kpi_pays k');
        }
        
        $this->db->join('ids_location l', 'k.locCode = l.locCode AND k.locType = l.locType');
        if($location_code == NULL){
            $this->db->where('k.locType', $location_type);
            if($location_type == 2){
                $this->db->join("($subQuery) r", 'r.locCode = l.parent');
            }
            
        }
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
        
        if($location_code == NULL){
            if($location_type == 1){
               $this->db->group_by(array("year", "location_name"));  
            }
            if($location_type == 2){
                $this->db->group_by(array("year", "location_name", "r.locName"));  
            }
        }
        else {
            $this->db->group_by(array("year", "location_name"));  
        }
        
        
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

    function get_harvestarea_totals_municities($location_code, $location_type,  $year_start, $year_end = NULL, 
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
        
        $this->db->select("concat(\"munName\",'\\n',\"provName\") AS location_name, g.geoCode AS map_ID, k.year AS year, k.areaHarv as value");
        $this->db->from("($munQuery) l");
        $this->db->join('kpi_pay_prism k', 'l.munCode = k.locCode AND l.provCode = k.provinceCode', 'left outer');
        $this->db->join('ids_geocodes g', 'l.munCode = g.locCode AND l.provCode = g.parent');
        $this->db->join('ids_ecosystem e', 'k.eco = e.eco'); 
        $this->db->where('k.sem', $sem_prism);
        $this->db->where('k.locType', '3');
        $this->db->where('g.locType', '3');
        $this->db->where('k.areaHarv >', '0');
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
    
    function get_harvestarea_aggregate_prism ($location_code = NULL, $location_type, $year_start = NULL, $year_end = NULL, $aggregate_function = NULL, $ecosystem, $order = NULL, $limit = NULL, $region_code = NULL){
        
        if($location_code == NULL){
            $this->db->select('locCode, locType, locName, sort, parent');
            $this->db->from('ids_location');
            $this->db->where('locType', $location_type);
            if($region_code != NULL){
                $this->db->where('parent', $region_code); 
            }
            $this->db->order_by('sort', 'ASC');  
            $subQuery =  $this->db->get_compiled_select();
        }
        
        if($aggregate_function == "MIN") {
            $this->db->select_min("k.areaHarv");
        }
        elseif($aggregate_function == "MAX") {
            $this->db->select_max("k.areaHarv");
        }
        elseif($aggregate_function == "MEDIAN") {
            $this->db->select('PERCENTILE_CONT(0.5) WITHIN GROUP(ORDER BY "k"."areaHarv")');             
        }
        
        $this->db->from('kpi_pay_prism k');
        $this->db->join('ids_location l', 'k.locCode = l.locCode AND k.locType = l.locType AND k.provinceCode = l.parent');
        $this->db->join('ids_ecosystem e', 'k.eco = e.eco');
        if($location_code == NULL){
            if($location_type == 3){
                $this->db->join("($subQuery) r", 'k.provinceCode = r.parent');
            }
            else {
                $this->db->join("($subQuery) r", 'k.provinceCode = r.locCode');
            }
            if($location_type == 1){
                $this->db->where('r.parent', $region_code); 
            }
        }
        $this->db->where('k.locType', '3');
        $this->db->where('k.eco', strval($ecosystem));
        $this->db->where('k.sem', '3');
        
        if(isset($year_end)){
            $this->db->where('k.year >=', $year_start);
            $this->db->where('k.year <=', $year_end);
        }
        else {
            $this->db->where('k.year', $year_start);
        }
        
        if($limit != null)  {
            $this->db->limit($limit);
        }
        
        $query = $this->db->get();
        $send_data = $query->result_array(); 
        $send_data = reset($send_data[key($send_data)]);
        return $send_data;
        
    }


    function get_areaharv_monthly_geocodes_municities($location_code, $location_type,  $year_start, $year_end = NULL, 
                                                        $ecosystem, $order = NULL, $limit = NULL, $region_code = NULL){
        
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

        // Step 1: Get latest year and month
        $this->db->select("MAX(k.year || LPAD(k.month::text, 2, '0')) AS latest_date", false);
        $this->db->from('kpi_areaha_prism k');
        $this->db->where('k.locType', '3');
        $this->db->where('k.areaHarv >', '0');
        $latest_row = $this->db->get()->row();

        if ($latest_row && $latest_row->latest_date) {
            $latest_year = substr($latest_row->latest_date, 0, 4);
            $latest_month = substr($latest_row->latest_date, 4, 2);
        } else {
            $latest_year = date('Y'); // fallback
            $latest_month = date('m');
        }
                
        $this->db->select('l.munName AS location_name, g.geoCode AS map_ID, k.year AS year, k.month as month, k.areaHarv as value');
        $this->db->select("concat(\"munName\",'\\n',\"provName\") AS location_name, g.geoCode AS map_ID, k.areaHarv as value");
        $this->db->from("($munQuery) l");
        $this->db->join('kpi_areaha_prism k', 'l.munCode = k.locCode AND l.provCode = k.provinceCode', 'left outer');
        $this->db->join('ids_geocodes g', 'l.munCode = g.locCode AND l.provCode = g.parent');
        $this->db->join('ids_ecosystem e', 'k.eco = e.eco'); 
        // $this->db->where('k.sem', $sem_prism);
        $this->db->where('k.locType', '3');
        $this->db->where('g.locType', '3');
        $this->db->where('k.areaHarv >', '0');

        $this->db->where('k.year', $latest_year);
        $this->db->where('k.month', $latest_month);
        
        if(isset($ecosystem)){
            $this->db->where('k.eco <=', $ecosystem);
        }

        $this->db->order_by('value', $order);

        $query=$this->db->get();
        return $query->result_array();
        
    }
}