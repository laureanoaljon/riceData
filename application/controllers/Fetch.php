<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fetch extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->model('mainmodel');
        $this->load->model('productionmodel');
        $this->load->model('yieldmodel');
        $this->load->model('areaharvestedmodel');
        $this->load->model('locationmodel');

        $this->load->helper('html');
        $this->load->helper('url');
        $this->load->helper('date');
        $this->load->helper('security');
        $this->load->library('session');  
        $this->load->library('form_validation');
        $this->load->library("pagination"); 
        $this->load->library('zip');
        $this->load->helper('file');
    }

    public function get_metadata(){
        $metadata_ids = $this->input->post('metadata_ids');

        $temp_array = $this->mainmodel->get_metadata_details($metadata_ids);
        $data['metadata'] = json_encode($temp_array);

        echo json_encode($data);
    }

    public function get_data(){
        $year = $this->input->post('year');
        $loc_type = $this->input->post('loc_type');
        $loc_code = $this->input->post('loc_code');
        $ecosystem = $this->input->post('ecosystem');
        $period = $this->input->post('period');
        $year_type = $this->input->post('year_type');

        $sem_prism = null;
        if ($period == '1' && $year_type == '2'){
            $sem_prism = '1'; // SEMESTER 1 (prism data)
        } else if ($period == '2' && $year_type == '2'){
            $sem_prism = '2'; // SEMESTER 2 (prism data)
        } else if ($period == '1' && $year_type == '1'){
            $sem_prism = '3'; // ANNUAL (prism data)
        }

        $location_code = range(1, 53);

        if ($loc_type && $loc_code){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($loc_code, $loc_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }

        // YIELD CODE #######################################################################################################################################
        // MAP SECTION
        // Regional
        $temp_array = $this->yieldmodel->get_yield_avgs_geocodes(null, '1', $year, null, $ecosystem, $period, $year_type, 'DESC');
        $data['regional_yield_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        // Provincial
        $temp_array = $this->yieldmodel->get_yield_avgs_geocodes(null, '2', $year, null, $ecosystem, $period, $year_type, 'DESC');
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 2);
        } 
        $data['provincial_yield_geo'] = json_encode($temp_array);
        $temp_array = array();

        // Municipal
        $temp_array = $this->yieldmodel->get_yield_avgs_geocodes_municities($location_code, '2', $year, null, $ecosystem, 'DESC', null, '999', $sem_prism);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 2, '.', '');
        } 
        $data['municipal_yield_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        // PRODUCTION CODE ########################################################################################################################

		// GET ALL MUNICIPALITY FOR MAP
        $location_code = range(1, 53);
        
        // $temp_array = $this->productionmodel->get_production_totals_municities_all($location_code, '3', $year - 20, $year, $ecosystem, 'DESC', null, null);
        $temp_array = $this->productionmodel->get_production_totals_municities($location_code, '2', $year, null, $ecosystem, 'DESC', null, '999', $sem_prism);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 2, '.', '');
        } 
        $data['municipal_production_geocoded'] = json_encode($temp_array);
        $temp_array = array();
        
        //maps section
        $temp_array = $this->productionmodel->get_production_totals_geocodes(null, '1', $year, null, $ecosystem, $period, $year_type, 'DESC');
        $data['regional_production_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        $temp_array = $this->productionmodel->get_production_totals_geocodes(null, '2', $year, null, $ecosystem, $period, $year_type, 'DESC');
        $data['provincial_production_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        //  Provincial Production
        // Top provincial production - Y3
        $temp_array = $this->productionmodel->get_production_totals(null, '2', $year, null, $ecosystem, $period, $year_type, 'DESC');
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['provincial_production_y3'] = json_encode($temp_array);
        $temp_array = array();

        // Regional Production
        $temp_array = $this->productionmodel->get_production_totals_regions(null, '1', $year, null, $ecosystem, $period, $year_type, 'DESC');
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['regional_production_y3'] = json_encode($temp_array);
        $temp_array = array();
        
        // Municipal Production
        $temp_array = $this->productionmodel->get_production_totals_municities($location_code, '2', $year, null, $ecosystem, 'DESC', null, '999', $sem_prism);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['municipal_production_y3'] = json_encode($temp_array);
        $temp_array = array();
        
        // Min and Max, municipalities
        $min = $this->productionmodel->get_production_aggregate_prism(NULL, '3', $year, NULL, 'MIN', $ecosystem, NULL, NULL, NULL);
        $max = $this->productionmodel->get_production_aggregate_prism(NULL, '3', $year, NULL, 'MAX', $ecosystem, NULL, NULL, NULL);
        $data['min_municipal'] = ceil($min / 1000) * 1000;
        $data['max_municipal'] = ceil($max / 1000) * 1000;
        $temp_array = array();

        // // AREA HARVES CODE #######################################################################################################################################
        // // Provincial

        // Top provincial harvested area geocodes
        $temp_array = $this->areaharvestedmodel->get_harvestarea_totals_geocodes(null, '2', $year, null, $ecosystem, $period, $year_type, 'DESC');
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['provincial_area_geo'] = json_encode($temp_array);
        $temp_array = array();

        // Provincial Harvested Area
        // Top harvest production - Y3
        $temp_array = $this->areaharvestedmodel->get_harvestarea_totals(null, '2', $year, null, $ecosystem, $period, $year_type, 'DESC');
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['provincial_area_y3'] = json_encode($temp_array);
        $temp_array = array();

        // Regional Harvest Area
        $temp_array = $this->areaharvestedmodel->get_harvestarea_totals_geocodes(null, '1', $year, null, $ecosystem, $period, $year_type, 'DESC');
        $data['regional_area_geo'] = json_encode($temp_array);
        $temp_array = array();

        $temp_array = $this->areaharvestedmodel->get_harvestarea_totals_regions(null, '1', $year, null, $ecosystem, $period, $year_type, 'DESC');
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['regional_area_y3'] = json_encode($temp_array);
        $temp_array = array();

        // Municipal Harvested Area
        $temp_array = $this->areaharvestedmodel->get_harvestarea_totals_municities($location_code, '2', $year, null, $ecosystem, 'DESC', null, '999', $sem_prism);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 2, '.', '');
        } 
        $data['municipal_area_geocoded'] = json_encode($temp_array);
        $temp_array = array();
        
        // Min and Max, municipalities
        $min = $this->areaharvestedmodel->get_harvestarea_aggregate_prism(NULL, '3', $year, NULL, 'MIN', $ecosystem, NULL, NULL, NULL);
        $max = $this->areaharvestedmodel->get_harvestarea_aggregate_prism(NULL, '3', $year, NULL, 'MAX', $ecosystem, NULL, NULL, NULL);
        $data['min_municipal_area'] = ceil($min / 1000) * 1000;
        $data['max_municipal_area'] = ceil($max / 1000) * 1000;
        
        echo json_encode($data);
    }

    // GET YEAR BY PERIOD
    public function get_year_by_period(){
        $map_boundary = $this->input->post('map_boundary');
        $period = $this->input->post('period');
        $year_type = $this->input->post('year_type');

        $sem_prism = '';
        if ($period == '1' && $year_type == '2'){
            $sem_prism = '1'; // SEMESTER 1 (prism data)
        } else if ($period == '2' && $year_type == '2'){
            $sem_prism = '2'; // SEMESTER 2 (prism data)
        } else if ($period == '1' && $year_type == '1'){
            $sem_prism = '3'; // ANNUAL (prism data)
        }

        // Get location coordinates
        $temp_array = $this->mainmodel->get_year_by_period($map_boundary, $period, $year_type, $sem_prism);
        $data['year'] = json_encode($temp_array);
        $temp_array = array();
        
        echo json_encode($data);
    }

    public function get_data_by_region(){
        $year = $this->input->post('year');
        $category = $this->input->post('category');
        $location_code = $this->input->post('location_id');
        $ecosystem = $this->input->post('ecosystem');
        $period = $this->input->post('period');
        $year_type = $this->input->post('year_type');

        $sem_prism = '';
        if ($period == '1' && $year_type == '2'){
            $sem_prism = '1'; // SEMESTER 1 (prism data)
        } else if ($period == '2' && $year_type == '2'){
            $sem_prism = '2'; // SEMESTER 2 (prism data)
        } else if ($period == '1' && $year_type == '1'){
            $sem_prism = '3'; // ANNUAL (prism data)
        }

        $location_type = '1';

        // Get location coordinates
        $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
        $data['location_coordinates'] = json_encode($temp_array);
        $temp_array = array();

        // ////////////////////////////////////////////////////////////////////// PRODUCTION CODE FOR REGION
        //maps section
        $temp_array = $this->productionmodel->get_production_totals_geocodes(null, '1', $year, null, $ecosystem, $period, $year_type, 'DESC', null, $location_code);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['provincial_production_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        $temp_array = $this->productionmodel->get_production_totals_municities($location_code, '2', $year, null, $ecosystem, 'DESC', null, $location_code, $sem_prism);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['municipal_production_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        // Top provincial production - Y3
        $temp_array = $this->productionmodel->get_production_totals(null, '2', $year, null, $ecosystem, $period, $year_type, 'DESC');
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['provincial_production_y3'] = json_encode($temp_array);
        $temp_array = array();

        $min = $this->productionmodel->get_production_aggregate_prism(NULL, '2', $year, NULL, 'MIN', 2, NULL, NULL, $location_code);
        $max = $this->productionmodel->get_production_aggregate_prism(NULL, '2', $year, NULL, 'MAX', 2, NULL, NULL, $location_code);
        $data['min_municipal_production'] = ceil($min / 1000) * 1000;
        $data['max_municipal_production'] = ceil($max / 1000) * 1000;
        $temp_array = array();

        // ////////////////////////////////////////////////////////////////////// YIELD CODE FOR REGION
        //maps section
        $temp_array = $this->yieldmodel->get_yield_avgs_geocodes(null, '1', $year, null, $ecosystem, $period, $year_type, 'DESC', null, $location_code);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 2);
        } 
        $data['provincial_yield_geo'] = json_encode($temp_array);
        $temp_array = array();


        $temp_array = $this->yieldmodel->get_yield_avgs_geocodes_current(null, '1', $year, $ecosystem, $location_code);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 2);
        } 
        $data['provincial_yield_geo_current'] = json_encode($temp_array);
        $temp_array = array();

        $temp_array = $this->yieldmodel->get_yield_avgs_geocodes_municities($location_code, '2', $year, null, $ecosystem, 'DESC', null, $location_code, $sem_prism);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 2);
        } 
        $data['municipal_yield_geo'] = json_encode($temp_array);
        $temp_array = array();


        /////////////////////////////////////////////////////////////////////// AREA HARVESTED CODE
        //maps section
        $temp_array = $this->areaharvestedmodel->get_harvestarea_totals_geocodes(null, '1', $year, null, $ecosystem, $period, $year_type, 'DESC', null, $location_code);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['provincial_area_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        $temp_array = $this->areaharvestedmodel->get_harvestarea_totals_municities($location_code, '2', $year, null, $ecosystem, 'DESC', null, $location_code, $sem_prism);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['municipal_area_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        // Top provincial production - Y3
        $temp_array = $this->areaharvestedmodel->get_harvestarea_totals(null, '1', $year, null, $ecosystem, $period, $year_type, 'DESC', null, $location_code);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['provincial_area_y3'] = json_encode($temp_array);
        $temp_array = array();

        // Min and Max, municipalities
        $min = $this->areaharvestedmodel->get_harvestarea_aggregate_prism(NULL, '2', $year, NULL, 'MIN', 2, NULL, NULL, $location_code);
        $max = $this->areaharvestedmodel->get_harvestarea_aggregate_prism(NULL, '2', $year, NULL, 'MAX', 2, NULL, NULL, $location_code);
        $data['min_municipal_area'] = ceil($min / 1000) * 1000;
        $data['max_municipal_area'] = ceil($max / 1000) * 1000;
        $temp_array = array();
        
        echo json_encode($data);
    }

    public function get_data_by_province(){
        $year = $this->input->post('year');
        $area_type = $this->input->post('area_type');
        $category = $this->input->post('category');
        $location_code = $this->input->post('location_id');
        $ecosystem = $this->input->post('ecosystem');
        $period = $this->input->post('period');
        $year_type = $this->input->post('year_type');

        $sem_prism = null;
        if ($period == '1' && $year_type == '2'){
            $sem_prism = '1'; // SEMESTER 1 (prism data)
        } else if ($period == '2' && $year_type == '2'){
            $sem_prism = '2'; // SEMESTER 2 (prism data)
        } else if ($period == '1' && $year_type == '1'){
            $sem_prism = '3'; // ANNUAL (prism data)
        }

        $location_type = '2';

        // Get location coordinates
        $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
        $data['location_coordinates'] = json_encode($temp_array);
        $temp_array = array();

        // ////////////////////////////////////////////////////////////////////// PRODUCTION CODE

        //maps section
        $temp_array = $this->productionmodel->get_production_totals_municities($location_code, '2', $year, null, $ecosystem, 'DESC', null, null, $sem_prism);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['municipal_production_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        // Min and Max, municipalities
        $min = $this->productionmodel->get_production_aggregate_prism(NULL, '3', $year, NULL, 'MIN', 2, NULL, NULL, $location_code);
        $max = $this->productionmodel->get_production_aggregate_prism(NULL, '3', $year, NULL, 'MAX', 2, NULL, NULL, $location_code);
        $data['min_municipal_production'] = ceil($min / 1000) * 1000;
        $data['max_municipal_production'] = ceil($max / 1000) * 1000;
        $temp_array = array();
        

        // ////////////////////////////////////////////////////////////////////// YIELD CODE
        //maps section
        $temp_array = $this->yieldmodel->get_yield_avgs_geocodes_municities($location_code, '2', $year, null, $ecosystem, 'DESC', null, null, $sem_prism);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 2);
        } 
        $data['municipal_yield_geo'] = json_encode($temp_array);
        $temp_array = array();
        

        /////////////////////////////////////////////////////////////////////// AREA HARVESTED CODE
        //maps section
        $temp_array = $this->areaharvestedmodel->get_harvestarea_totals_municities($location_code, '2', $year, null, $ecosystem, 'DESC', null, null, $sem_prism);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['municipal_area_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        // Min and Max, municipalities
        $min = $this->areaharvestedmodel->get_harvestarea_aggregate_prism(NULL, '3', $year, NULL, 'MIN', 2, NULL, NULL, $location_code);
        $max = $this->areaharvestedmodel->get_harvestarea_aggregate_prism(NULL, '3', $year, NULL, 'MAX', 2, NULL, NULL, $location_code);
        $data['min_municipal_area'] = ceil($min / 1000) * 1000;
        $data['max_municipal_area'] = ceil($max / 1000) * 1000;
        $temp_array = array();

        // Municipal (Area) - Current
        $temp_array = $this->areaharvestedmodel->get_areaharv_monthly_geocodes_municities($location_code, '2', $year, null, '2', 'DESC', NULL, NULL);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 2, '.', '');
        } 
        $data['municipal_current_monthly_areaha_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_income_data(){
        $year = $this->input->post('year');
        $loc_type = $this->input->post('loc_type');
        $loc_code = $this->input->post('loc_code');
        $ecosystem = $this->input->post('ecosystem');
        $period = $this->input->post('period');

        $loctype = '1';
        $loccode = NULL;

        if ($loc_type && $loc_code){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($loc_code, $loc_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();

            // $loctype = $loc_type;
            // $loccode = $loc_code;
        }

        // INCOMES ####################################################################################################################################
        // Total Costs Regional (get_cost_net) $temp_array = $this->incomemodel->get_costs_net(NULL, '1', $ecosystem, $year, NULL, 'ASC');

        $temp_array = $this->incomemodel->get_net_costs_gross($loccode, $loctype, $ecosystem, $year, NULL, 'ASC', NULL, $period, NULL);
        foreach ($temp_array as &$active_array){
            $active_array['costs'] = number_format($active_array['costs'], 0, '.', '');
            $active_array['net'] = number_format($active_array['net'], 0, '.', '');
            $active_array['gross'] = number_format($active_array['gross'], 0, '.', '');
        }
        $data['regional_costs_all'] = json_encode($temp_array);
        $temp_array = array();
        
        echo json_encode($data);
    }

    public function get_valuation_data(){
        $year = $this->input->post('year');
        $loc_type = $this->input->post('loc_type');
        $loc_code = $this->input->post('loc_code');

        $loctype = '1';
        $loccode = NULL;

        if ($loc_type && $loc_code){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($loc_code, $loc_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();

            // $loctype = $loc_type;
            // $loccode = $loc_code;
        }

        // VALUATION ####################################################################################################################################

        $temp_array = $this->valuationmodel->get_valuation_geocoded($loccode, $loctype, $year);
        $data['valuation_geocoded'] = json_encode($temp_array);
        $temp_array = array();
        
        echo json_encode($data);
    }

    public function get_prices_data(){
        $year = $this->input->post('year');
        $loc_type = $this->input->post('loc_type');
        $loc_code = $this->input->post('loc_code');
        $period = $this->input->post('period'); // 1, 2, 3, 4
        $period_unit = $this->input->post('period_type'); // 1 to 12
        $rice_price = $this->input->post('rice_price'); // farmgate, wholesale, retail
        $rice_class = $this->input->post('rice_class'); // fancy, ordinary, well-milled, regular

        $map_boundary = $this->input->post('map_boundary');

        if ($map_boundary == 'REGION' && $loc_code == "999"){
            $loc_type = '1';
            $loc_code = NULL;
        } else if($map_boundary == 'PROVINCE' && $loc_code == "999"){
            $loc_type = '2';
            $loc_code = NULL;
        }

        // $loctype = '1';
        // $loccode = NULL;

        if ($loc_type && $loc_code){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($loc_code, $loc_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();

        }

        // To get all provinces
        if ($loc_type == '2' && $loc_code != '999'){
            $loc_type = '2';
            $loc_code = NULL;
        }

        if ($rice_price == "wholesale") {
            /////////////////////////////////////////////////////////////////////// WHOLESALE PRICE CODE
            // Maps section provincial
            // $temp_array = $this->pricesmodel->get_wholesale_prices_geocodes($loc_code, $loc_type, $year, null, null, $period, $rice_class, 'DESC', null, null, $period_unit);
            // foreach ($temp_array as &$active_array) {
            //     $active_array['value'] = number_format($active_array['value'] ?? 0, 2, '.', ''); // Replace null with 0
            // } 
            // $data['provincial_nfarmgate_prices_geocoded'] = json_encode($temp_array);
            // $temp_array = array();
        
            // Maps section regional
            $temp_array = $this->pricesmodel->get_wholesale_prices_geocodes($loc_code, $loc_type, $year, null, null, $period, $rice_class, 'DESC', null, null, $period_unit);
            foreach ($temp_array as &$active_array) {
                $active_array['value'] = number_format($active_array['value'] ?? 0, 2, '.', ''); // Replace null with 0
            } 
            $data['nfarmgate_prices_geocoded'] = json_encode($temp_array);
            $temp_array = array();
        } else if ($rice_price == "retail") {
            // /////////////////////////////////////////////////////////////////////// RETAIL PRICE CODE
            // // Maps section provincial
            // $temp_array = $this->pricesmodel->get_retail_prices_geocodes($loc_code, $loc_type, $year, null, null, $period, $rice_class, 'DESC', null, null, $period_unit);
            // foreach ($temp_array as &$active_array) {
            //     $active_array['value'] = number_format($active_array['value'] ?? 0, 2, '.', ''); // Replace null with 0
            // } 
            // $data['provincial_nfarmgate_prices_geocoded'] = json_encode($temp_array);
            // $temp_array = array();
        
            // Maps section regional
            $temp_array = $this->pricesmodel->get_retail_prices_geocodes($loc_code, $loc_type, $year, null, null, $period, $rice_class, 'DESC', null, null, $period_unit);
            foreach ($temp_array as &$active_array) {
                $active_array['value'] = number_format($active_array['value'] ?? 0, 2, '.', ''); // Replace null with 0
            } 
            $data['nfarmgate_prices_geocoded'] = json_encode($temp_array);
            $temp_array = array();
        } else {

            /////////////////////////////////////////////////////////////////////// FARMGATE PRICE
            //maps section provincial
            $temp_array = $this->pricesmodel->get_farmgate_prices_geocodes(null, '2', $year, null, null, $period, $rice_class, 'DESC', null, null, $period_unit);
            foreach ($temp_array as &$active_array){
                $active_array['value'] = number_format($active_array['value'], 2, '.', '');
            } 
            $data['provincial_farmgate_prices_geocoded'] = json_encode($temp_array);
            $temp_array = array();

            //maps section regional
            $temp_array = $this->pricesmodel->get_farmgate_prices_geocodes(null, '1', $year, null, null, $period, $rice_class, 'DESC', null, null, $period_unit);
            foreach ($temp_array as &$active_array){
                $active_array['value'] = number_format($active_array['value'], 2, '.', '');
            } 
            $data['regional_farmgate_prices_geocoded'] = json_encode($temp_array);
            $temp_array = array();
        }


        echo json_encode($data);
    }

    public function get_prices_data_by_region(){
        $year = $this->input->post('year');
        $loc_type = $this->input->post('loc_type');
        $loc_code = $this->input->post('loc_code');
        $period = $this->input->post('period'); // 1, 2, 3, 4
        $period_unit = $this->input->post('period_type'); // 1 to 12
        $rice_price = $this->input->post('rice_price'); // farmgate, wholesale, retail
        $rice_class = $this->input->post('rice_class'); // fancy, ordinary, well-milled, regular
        
        // $loctype = '1';
        // $loccode = NULL;

        if ($loc_type && $loc_code){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($loc_code, $loc_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();

        }

        if ($rice_price == "farmgate"){
            $temp_array = $this->pricesmodel->get_farmgate_prices_geocodes_provinces(null, '1', $year, null, '2', 'DESC', null, $loc_code, $period, $period_unit, $rice_price, $rice_class);
            $data['farmgate_prices_province_geocoded'] = json_encode($temp_array);
            $temp_array = array();
        } else { // if retail or wholesale
            $temp_array = $this->pricesmodel->get_nfarmgate_prices_geocodes_provinces(null, '1', $year, null, '2', 'DESC', null, $loc_code, $period, $period_unit, $rice_price, $rice_class);
            $data['farmgate_prices_province_geocoded'] = json_encode($temp_array);
            $temp_array = array();
        }

        echo json_encode($data);
    }

    public function get_fertprices_data(){
        // $year = $this->input->post('year');
        $category = 'fertilizer_prices';
        $year = $this->get_lastest_year($category);
        $last_year = $this->get_last_year($category);

        $loc_type = $this->input->post('loc_type');
        $loc_code = $this->input->post('loc_code');
        $fertilizer = $this->input->post('fertilizer');
        // $year_type = $this->input->post('year_type');

        //Fertilizer Provinces Map
        $temp_array = $this->fertpricemodel->get_fertprices_locations_geocodes('2', $year, NULL, 'DESC', $fertilizer);
        $data['fertprice_provinces_maps'] = json_encode($temp_array);
        $temp_array = array();

        // NO MAP ID
        // $temp_array = $this->fertpricemodel->get_fertprices_locations('2', $year, NULL, 'DESC', $fertilizer);
        // $data['fert_provinces'] = json_encode($temp_array);
        // $temp_array = array();

        //Fertilizer Regions Map
        $temp_array = $this->fertpricemodel->get_fertprices_locations_geocodes('1', $year, NULL, 'DESC', $fertilizer);
        $data['fertprice_regions_maps'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_milled_prices_data(){
        $year = $this->input->post('year');
        $loc_type = $this->input->post('loc_type');
        $loc_code = $this->input->post('loc_code');
        $milled_category = $this->input->post('milled_category');
        $type_of_milled = $this->input->post('type_of_milled');
        $period = $this->input->post('period');
        $period_unit = $this->input->post('period_unit');

        if ($milled_category == "wholesale"){
            /////////////////////////////////////////////////////////////////////// WHOLESALE PRICE CODE
            //maps section provincial
            $temp_array = $this->pricesmodel->get_wholesale_prices_geocodes(null, '2', $year, null, null, $period, $type_of_milled, 'DESC', null, null, $period_unit);
            foreach ($temp_array as &$active_array){
                $active_array['value'] = number_format($active_array['value'], 2, '.', '');
            } 
            $data['provincial_milled_prices_geocoded'] = json_encode($temp_array);
            $temp_array = array();

            //maps section regional
            $temp_array = $this->pricesmodel->get_wholesale_prices_geocodes(null, '1', $year, null, null, $period, $type_of_milled, 'DESC', null, null, $period_unit);
            foreach ($temp_array as &$active_array){
                $active_array['value'] = number_format($active_array['value'], 2, '.', '');
            } 
            $data['regional_milled_prices_geocoded'] = json_encode($temp_array);
            $temp_array = array();
        } else {
            /////////////////////////////////////////////////////////////////////// WHOLESALE PRICE CODE
            //maps section provincial
            $temp_array = $this->pricesmodel->get_retail_prices_geocodes(null, '2', $year, null, null, $period, $type_of_milled, 'DESC', null, null, $period_unit);
            foreach ($temp_array as &$active_array){
                $active_array['value'] = number_format($active_array['value'], 2, '.', '');
            } 
            $data['provincial_milled_prices_geocoded'] = json_encode($temp_array);
            $temp_array = array();

            //maps section regional
            $temp_array = $this->pricesmodel->get_retail_prices_geocodes(null, '1', $year, null, null, $period, $type_of_milled, 'DESC', null, null, $period_unit);
            foreach ($temp_array as &$active_array){
                $active_array['value'] = number_format($active_array['value'], 2, '.', '');
            } 
            $data['regional_milled_prices_geocoded'] = json_encode($temp_array);
            $temp_array = array();
        }


        echo json_encode($data);
    }

    public function get_demographics_data(){
        $year = $this->input->post('year');
        $loc_type = $this->input->post('loc_type');
        $loc_code = $this->input->post('loc_code');
        $demographics = $this->input->post('demographics');

        // GET ALL SOCIO GEOCODED PER PROVINCE
        $temp_array = $this->sociomodel->get_all_socio_totals_geocodes(null, '2', $year, null, '2', 'ASC');
        $data['provincial_all_socio_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_prices_latest_24_data(){
        $period = $this->input->post('period');

        // GET ALL SOCIO GEOCODED PER PROVINCE
        $temp_array = $this->pricesmodel->get_latest_24_prices_data($period);
        $data['latest_prices_data'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);        
    }

    public function get_available_year_prism(){

        $table = $this->input->post('table');
    
        if (!empty($table)) {
            $temp_array = $this->mainmodel->get_year_data($table, 'ASC');

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

    public function get_available_sqm(){
        
        $table = $this->input->post('table');
        $year = $this->input->post('year');
        $year_type = $this->input->post('year_type');
    
        if (!empty($table)) {
            $temp_array = $this->mainmodel->get_sqm_data($table, $year, $year_type, 'DESC');

            // Extract just the year values from the array
            $period_units = array_column($temp_array, 'periodUnit');
    
            // Return years as JSON
            $data['period_units'] = $period_units;
            echo json_encode($data);
        } else {
            // Return an error if the table is not set
            echo json_encode(['error' => 'Invalid category']);
        }
    }

    public function get_available_month_by_year(){
        $year = $this->input->post('year');
        $table = $this->input->post('table');

        // GET ALL SOCIO GEOCODED PER PROVINCE
        $temp_array = $this->mainmodel->get_months_by_year($year, $table);
        $data['months'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);        
    }

    public function get_available_inflation_month_by_year(){
        $year = $this->input->post('year');
        $table = $this->input->post('table');

        // GET ALL SOCIO GEOCODED PER PROVINCE
        $temp_array = $this->mainmodel->get_inflation_months_by_year($year, $table);
        $data['months'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);        
    }

    public function get_range_month_year(){
        $year = $this->input->post('year');
        $year_range = $this->input->post('year_range');
        $table = $this->input->post('table');

        // GET ALL SOCIO GEOCODED PER PROVINCE
        $temp_array = $this->mainmodel->get_ranges_month_year($year, $table, $year_range);
        $data['months_years'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_range_fert_data(){
        $year = $this->input->post('year');
        $year_range = $this->input->post('year_range');

        $table1 = 'kpi_wfertprices';
        $table2 = 'kpi_fertprices';

        // GET Week Years
        $temp_array = $this->mainmodel->get_ranges_week_year($year, $table2, $year_range);
        $data['weeks_years'] = json_encode($temp_array);
        $temp_array = array();

        // GET Month Years
        $temp_array = $this->mainmodel->get_ranges_month_year($year, $table1, $year_range);
        $data['months_years'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_fertprices_monthly_data(){

        // GET ALL SOCIO GEOCODED PER PROVINCE
        $temp_array = $this->fertpricemodel->get_fertprices_monthly_data();
        $data['monthly_fertprices'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);        
    }

    public function get_stocks_monthly_data(){
        // GET ALL SOCIO GEOCODED PER PROVINCE
        $temp_array = $this->stockmodel->get_stocks_monthly_data();
        $data['monthly_stocks'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);        
    }

    public function get_inflation_monthly_data(){
        // GET ALL SOCIO GEOCODED PER PROVINCE
        $temp_array = $this->inflationmodel->get_inflation_monthly_data();
        $data['monthly_inflation'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);        
    }

    public function get_inflation_yearly_data(){
        // GET ALL SOCIO GEOCODED PER PROVINCE
        $temp_array = $this->inflationmodel->get_inflation_yearly_data();
        $data['annual_inflation'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);        
    }

    // //////////// RICE TECHNOLOGIES //////////////////// //
    public function get_awareness_data(){
        $technology = $this->input->post('technology');
        $year = $this->input->post('season');
        $category = $this->input->post('category');

        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        ///////////////////////////////////////////////////////////////////////
        //maps section provincial
        $temp_array = $this->awarenessmodel->get_adopted_geocoded(null, $location_type, (string)$year, null, '2', 'ASC', null, (int)$category, (int)$technology);
        $data['technology_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_awareness_data_by_region(){
        $technology = $this->input->post('technology');
        $season = $this->input->post('season');
        $category = $this->input->post('category');
        $location_code = $this->input->post('location_id');

        $location_type = '1';
        $year = $season;

        // Get location coordinates
        $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
        $data['location_coordinates'] = json_encode($temp_array);
        $temp_array = array();

        // ////////////////////////////////////////////////////////////////////// PRODUCTION CODE FOR REGION
        //maps section
        $temp_array = $this->awarenessmodel->get_adopted_totals_geocodes(null, $location_type, $year, null, '2', 'DESC', null, $location_code, (int)$category, (int)$technology);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['provincial_awareness_geocoded'] = json_encode($temp_array);
        $temp_array = array();
        
        echo json_encode($data);
    }

    public function get_cropest_data(){
        $method = $this->input->post('method');
        $year = $this->input->post('season');

        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        ///////////////////////////////////////////////////////////////////////
        //maps section provincial
        $temp_array = $this->cropestmodel->get_cropest_geocoded(NULL, $location_type, (string)$year, NULL, '2', 'ASC', $method);
        $data['all_cropest_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        // // ////////////////////////////////////////////////////////////////////// PRODUCTION CODE FOR REGION
        // //maps section
        // $temp_array = $this->awarenessmodel->get_adopted_totals_geocodes(null, $location_type, $year, null, '2', 'DESC', null, $location_code, $category, $technology);
        // foreach ($temp_array as &$active_array){
        //     $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        // } 
        // $data['provincial_awareness_geocoded'] = json_encode($temp_array);
        // $temp_array = array();

        echo json_encode($data);
    }

    public function get_cropest_data_by_region(){
        $method = $this->input->post('method');
        $year = $this->input->post('season');

        $location_code = $this->input->post('location_id');
        $location_type = '1';

        // Get location coordinates
        $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
        $data['location_coordinates'] = json_encode($temp_array);
        $temp_array = array();

        // ////////////////////////////////////////////////////////////////////// PRODUCTION CODE FOR REGION
        //maps section
        $temp_array = $this->cropestmodel->get_cropest_totals_geocoded(null, $location_type, (string)$year, null, '2', 'DESC', null, $location_code, $method);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['provincial_cropest_geocoded'] = json_encode($temp_array);
        $temp_array = array();
        
        echo json_encode($data);
    }

    public function get_nutrient_data_fert_grade(){
        $filter = $this->input->post('filter');
        $year = $this->input->post('season');

        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        ///////////////////////////////////////////////////////////////////////
        //maps section provincial
        $temp_array = $this->nutrientmodel->get_fertgrade_geocoded(null, $location_type, (string)$year, NULL, '2', 'ASC', $filter);
        $data['all_nutrient_management_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_nutrient_data_fertgrade_by_region(){
        $filter = $this->input->post('filter');
        $season = $this->input->post('season');

        $location_code = $this->input->post('location_id');

        $location_type = '1';
        $year = $season;

        // Get location coordinates
        $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
        $data['location_coordinates'] = json_encode($temp_array);
        $temp_array = array();

        // ////////////////////////////////////////////////////////////////////// PRODUCTION CODE FOR REGION
        //maps section
        $temp_array = $this->nutrientmodel->get_fertgrade_totals_geocoded(null, $location_type, (string)$year, null, '2', 'DESC', null, $location_code, $filter);

        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 

        $data['provincial_nutrient_management_geocoded'] = json_encode($temp_array);
        $temp_array = array();
        
        echo json_encode($data);
    }

    public function get_nutrient_management_data_by_region(){
        $filter = $this->input->post('filter');
        $season = $this->input->post('season');
        $filter_radio = $this->input->post('filter_radio');

        $location_code = $this->input->post('location_id');

        $location_type = '1';
        $year = $season;

        // Get location coordinates
        $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
        $data['location_coordinates'] = json_encode($temp_array);
        $temp_array = array();

        // ////////////////////////////////////////////////////////////////////// PRODUCTION CODE FOR REGION
        //maps section
        if ($filter_radio == 'by_element'){
            $temp_array = $this->nutrientmodel->get_fertilizer_totals_geocoded(null, $location_type, (string)$year, null, '2', 'DESC', null, $location_code, $filter);
        } else {
            $temp_array = $this->nutrientmodel->get_fertuse_totals_geocoded(null, $location_type, (string)$year, null, '2', 'DESC', null, $location_code, $filter);
        }

        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 

        $data['provincial_nutrient_management_geocoded'] = json_encode($temp_array);
        $temp_array = array();
        
        echo json_encode($data);
    }

    public function get_nutrient_management_data(){
        $filter = $this->input->post('filter');
        $year = $this->input->post('season');
        $filter_radio = $this->input->post('filter_radio');

        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        ///////////////////////////////////////////////////////////////////////
        //maps section provincial
        if ($filter_radio == 'by_element'){
            $temp_array = $this->nutrientmodel->get_fertilizer_geocoded(null, $location_type, (string)$year, NULL, '2', 'ASC', $filter);
        } else {
            $temp_array = $this->nutrientmodel->get_fertuse_geocoded(null, $location_type, (string)$year, NULL, '2', 'ASC', $filter);
        }
        
        $data['all_nutrient_management_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_all_nutrient_management_data(){
        $filter = $this->input->post('filter');
        $year = $this->input->post('season');
        $filter_radio = $this->input->post('filter_radio');

        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        $temp_array = $this->nutrientmodel->get_all_fertuse_geocoded(null, $location_type, (string)$year, NULL, '2', 'ASC', $filter);
        $data['all_nutrient_management_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_all_nutrient_management_data_by_region(){
        $filter = $this->input->post('filter');
        $season = $this->input->post('season');
        $filter_radio = $this->input->post('filter_radio');

        $location_code = $this->input->post('location_id');

        $location_type = '1';
        $year = $season;

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        $temp_array = $this->nutrientmodel->get_all_fertuse_totals_geocoded(null, $location_type, (string)$year, NULL, '2', 'ASC', $location_code, $filter);
        $data['provincial_all_nutrient_management_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_top_nutrient_by_fertilizer(){
        $year = $this->input->post('season');
        $location_code = $this->input->post('location_code');
        $location_type = $this->input->post('location_type');

        // Set location_code and location_type to default if not provided
        if (empty($location_code)) {
            $location_code = 999; // Ensure this is an integer
            $location_type = 2;
        } else {
            $location_code = $location_code;
            $location_type = $location_type;
        }
        
        $temp_array = $this->nutrientmodel->get_top_nutrient_in_fertuse($year, $location_code, $location_type);
        $data['top_nutrient'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_top_nutrient_fertgrade(){
        $year = $this->input->post('season');
        $location_code = $this->input->post('location_code');
        $location_type = $this->input->post('location_type');

        // Set location_code to NULL if not provided, else cast to integer
        if ($location_code == '') {
            $location_code = '999';
            $location_type = '2';
        } else {
            $location_code = $location_code;
            $location_type = $location_type;
        }
        
        $temp_array = $this->nutrientmodel->get_top_nutrient_in_fertgrade($year, $location_code, $location_type);
        $data['top_nutrient'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    //////////////////////// PESTICIDES /////////////////////////////

    public function get_ingredient_by_pesticide(){
        $year = $this->input->post('season');
        $type = $this->input->post('type');
        
        ///////////////////////////////////////////////////////////////////////
        $temp_array = $this->pestmodel->get_all_ingredients((string)$year, $type);
        $data['all_ingredients'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_top5_pesticides_geocoded(){
        $year = $this->input->post('season');
        $type = $this->input->post('type');

        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        //maps section provincial
        $temp_array = $this->pestmodel->get_top5_pesticides_geocoded(null, $location_type, (string)$year, NULL, '2', 'ASC', $type);
        $data['top5_pesticides_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_top5_pesticides_by_region_geocoded(){
        $year = $this->input->post('season');
        $type = $this->input->post('type');

        $location_code = $this->input->post('location_id');

        $location_type = '1';

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        //maps section provincial
        $temp_array = $this->pestmodel->get_top5_pesticides_by_region_geocoded(null, $location_type, (string)$year, NULL, '2', 'ASC', $type, $location_code);
        $data['top5_pesticides_by_region_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_top5_pesticides(){
        $year = $this->input->post('season');
        $type = $this->input->post('type');
        
        ///////////////////////////////////////////////////////////////////////
        $temp_array = $this->pestmodel->get_top5_pesticides((string)$year, $type);
        $data['top_pesticides'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_pesticides_data(){
        $type = $this->input->post('type');
        $year = $this->input->post('season');
        $ingredient = $this->input->post('ingredient');

        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        ///////////////////////////////////////////////////////////////////////
        //maps section provincial
        $temp_array = $this->pestmodel->get_pesticides_geocoded(null, $location_type, (string)$year, NULL, '2', 'ASC', $type, $ingredient);
        $data['pesticides_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_pesticides_data_by_region(){
        $type = $this->input->post('type');
        $season = $this->input->post('season');
        $ingredient = $this->input->post('ingredient');

        $location_code = $this->input->post('location_id');

        $location_type = '1';
        $year = $season;

        // Get location coordinates
        $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
        $data['location_coordinates'] = json_encode($temp_array);
        $temp_array = array();

        // ////////////////////////////////////////////////////////////////////// PRODUCTION CODE FOR REGION
        //maps section
        $temp_array = $this->pestmodel->get_pesticides_totals_geocoded(null, $location_type, (string)$year, null, '2', 'DESC', null, $location_code, $type, $ingredient);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['provincial_pesticides_geocoded_by_region'] = json_encode($temp_array);
        $temp_array = array();
        
        echo json_encode($data);
    }

    public function get_all_pest_methods_geocoded(){
        $management = $this->input->post('management');
        $year = $this->input->post('season');

        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        ///////////////////////////////////////////////////////////////////////
        $temp_array = $this->pestmodel->get_all_pest_methods_geocoded(NULL, $location_type, (string)$year, NULL, '2', 'ASC', $management);
        $data['all_pest_methods_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_all_pest_methods_geocoded_by_region(){
        $management = $this->input->post('management');
        $year = $this->input->post('season');

        $location_code = $this->input->post('location_id');

        $location_type = '1';

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        ///////////////////////////////////////////////////////////////////////
        $temp_array = $this->pestmodel->get_all_pest_methods_geocoded_by_region(null, $location_type, (string)$year, NULL, '2', 'ASC', $location_code, $management);
        $data['all_pest_methods_geocoded_by_region'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    // /////////// AWARENESS ///////////////////// //

    public function get_awareness_technologies(){
        $category = $this->input->post('category');
        $year = $this->input->post('season');
        $location_code = $this->input->post('location');
        $location_type = $this->input->post('location_type');

        // Get awareness technology
        $temp_array = $this->mainmodel->get_awareness_technologies((int)$category, (string)$year, $location_code, $location_type);
        foreach ($temp_array as &$tech) {
            // Remove newlines (both literal newlines and escaped \n characters)
            $tech['technology'] = str_replace(array("\n", "\\n", "\r"), '', $tech['technology']);
        }
        // Now assign the cleaned array to the $data array
        $data['technologies'] = $temp_array;
        $temp_array = array();
        
        echo json_encode($data);
    }

    //////// MACHINERY ///////

    public function get_machineries(){
        $year = $this->input->post('season');
        $location_code = $this->input->post('location');
        $location_type = $this->input->post('location_type');

        // Get awareness technology
        $temp_array = $this->mainmodel->get_machineries((string)$year, (int)$location_code, $location_type);
        $filtered_array = [];

        foreach ($temp_array as &$machine) {
            // Remove newlines (both literal newlines and escaped \n characters)
            $machine['machine'] = str_replace(array("\n", "\\n", "\r"), '', $machine['machine']);

            // Exclude entries with "Others"
            if (trim($machine['machine']) !== 'Others') {
                $filtered_array[] = $machine;
            }
        }
        // Now assign the cleaned array to the $data array
        $data['machineries'] = $filtered_array;
        $temp_array = array();
        
        echo json_encode($data);
    }

    public function get_machineries_data_by_region(){
        $year = $this->input->post('season');
        $location_code = $this->input->post('location');
        $machine = $this->input->post('machinery');

        $location_type = '1';

        // Get location coordinates
        $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
        $data['location_coordinates'] = json_encode($temp_array);
        $temp_array = array();

        // ////////////////////////////////////////////////////////////////////// PRODUCTION CODE FOR REGION
        //maps section
        $temp_array = $this->machinerymodel->get_machineries_region_geocodes(null, $location_type, $year, null, '2', 'DESC', null, $location_code, (string)$machine);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['provincial_machineries_geocoded'] = json_encode($temp_array);
        $temp_array = array();
        
        echo json_encode($data);
    }

    public function get_machinery_data(){
        $machine = $this->input->post('machinery');
        $year = $this->input->post('season');

        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        //maps section provincial
        $temp_array = $this->machinerymodel->get_machineries_geocoded(null, $location_type, (string)$year, null, '2', 'ASC', null, (string)$machine);
        $data['machineries_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    // ///////////////// Seed And Varieties ////////////////////////////
    public function get_seed_class_data(){
        $class = $this->input->post('seed_class');
        $year = $this->input->post('season');
        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        ///////////////////////////////////////////////////////////////////////
        //maps section provincial
        $temp_array = $this->seedsvarietiesmodel->get_seed_class_geocoded(NULL, $location_type, (string)$year, NULL, '2', 'ASC', $class);
        $data['seed_class_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_seed_class_data_by_region(){
        $class = $this->input->post('seed_class');
        $year = $this->input->post('season');
        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');

        // Get location coordinates
        $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
        $data['location_coordinates'] = json_encode($temp_array);
        $temp_array = array();

        // ////////////////////////////////////////////////////////////////////// PRODUCTION CODE FOR REGION
        //maps section
        $temp_array = $this->seedsvarietiesmodel->get_seed_class_geocoded_by_region(null, $location_type, (string)$year, null, '2', 'DESC', null, $location_code, $class);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['provincial_seed_class_geocoded'] = json_encode($temp_array);
        $temp_array = array();
        
        echo json_encode($data);
    }

    public function get_seed_class_data_by_region_by_province(){
        $class = $this->input->post('seed_class');
        $year = $this->input->post('season');
        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');
        $orig_location_type = $this->input->post('orig_location_type');

        $temp_array = $this->locationmodel->get_parent_code($location_code, $orig_location_type);
        $parent_code = $temp_array['parent_code'];
        $data['parent_code'] = $parent_code;

        // Get location coordinates
        $temp_array = $this->locationmodel->get_coordinates($location_code, $orig_location_type);
        $data['location_coordinates'] = json_encode($temp_array);
        $temp_array = array();

        // ////////////////////////////////////////////////////////////////////// PRODUCTION CODE FOR REGION
        //maps section
        $temp_array = $this->seedsvarietiesmodel->get_seed_class_geocoded_by_region(null, $location_type, (string)$year, null, '2', 'DESC', null, $parent_code, $class);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['provincial_seed_class_geocoded'] = json_encode($temp_array);
        $temp_array = array();
        
        echo json_encode($data);
    }

    public function get_seeding_rate_data(){
        $seed_rate_filter = $this->input->post('seed_rate_filter');
        $year = $this->input->post('season');
        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        ///////////////////////////////////////////////////////////////////////
        //maps section provincial
        $temp_array = $this->seedsvarietiesmodel->get_seeding_rate_geocoded(NULL, $location_type, (string)$year, NULL, '2', 'ASC', $seed_rate_filter);
        $data['seeding_rate_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_seeding_rate_seedclass_data(){
        $seed_rate_filter = $this->input->post('seed_rate_filter');
        $year = $this->input->post('season');
        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        ///////////////////////////////////////////////////////////////////////
        //maps section provincial
        $temp_array = $this->seedsvarietiesmodel->get_seeding_rate_seedclass_geocoded(NULL, $location_type, (string)$year, NULL, '2', 'ASC', $seed_rate_filter);
        $data['seeding_rate_seedclass_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_seeding_rate_seedclass_data_by_region(){
        $seed_rate_filter = $this->input->post('seed_rate_filter');
        $year = $this->input->post('season');

        $location_code = $this->input->post('location_id');

        $location_type = '1';

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        ///////////////////////////////////////////////////////////////////////
        //maps section provincial
        $temp_array = $this->seedsvarietiesmodel->get_seeding_rate_seedclass_geocoded_by_region(NULL, $location_type, (string)$year, NULL, '2', 'ASC', $location_code, $seed_rate_filter);
        $data['seeding_rate_seedclass_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_seeding_rate_data_by_region(){
        $seed_rate_filter = $this->input->post('seed_rate_filter');
        $year = $this->input->post('season');

        $location_code = $this->input->post('location_id');

        $location_type = '1';

        // Get location coordinates
        $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
        $data['location_coordinates'] = json_encode($temp_array);
        $temp_array = array();

        // ////////////////////////////////////////////////////////////////////// PRODUCTION CODE FOR REGION
        //maps section
        $temp_array = $this->seedsvarietiesmodel->get_seeding_rate_geocoded_by_region(null, $location_type, (string)$year, null, '2', 'DESC', null, $location_code, $seed_rate_filter);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['provincial_seeding_rate_geocoded'] = json_encode($temp_array);
        $temp_array = array();
        
        echo json_encode($data);
    }

    public function get_varieties_data(){
        $filter = $this->input->post('filter');
        $radio_value = $this->input->post('radio_value');
        $year = $this->input->post('season');
        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        ///////////////////////////////////////////////////////////////////////
        //maps section provincial
        $temp_array = $this->seedsvarietiesmodel->get_top_varieties_geocoded(NULL, $location_type, (string)$year, NULL, '2', 'ASC', NULL, $radio_value, $filter);
        $data['top_varieties_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_top_varieties(){
        $year = $this->input->post('season');
        $type = $this->input->post('radio_value');
        
        ///////////////////////////////////////////////////////////////////////
        $temp_array = $this->seedsvarietiesmodel->get_top_varieties((string)$year, $type);
        $data['top_varieties'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_top_varieties_by_location(){
        $year = $this->input->post('season');
        $type = $this->input->post('radio_value');
        $locType = $this->input->post('location_type');
        $locCode = $this->input->post('location_id');
        
        ///////////////////////////////////////////////////////////////////////
        $temp_array = $this->seedsvarietiesmodel->get_top_varieties_by_location((string)$year, $type, $locCode, $locType);
        $data['top_varieties_by_location'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_top_varieties_count_by_location(){
        $year = $this->input->post('season');
        $type = $this->input->post('radio_value');
        $locType = $this->input->post('location_type');
        $locCode = $this->input->post('location_id');
        
        ///////////////////////////////////////////////////////////////////////
        $temp_array = $this->seedsvarietiesmodel->get_top_varieties_count_by_location((string)$year, $type, $locCode, $locType);
        $data['top_varieties_count'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_all_top3_varieties(){
        $year = $this->input->post('season');
        $type = $this->input->post('radio_value');

        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');
        
        ///////////////////////////////////////////////////////////////////////
        $temp_array = $this->seedsvarietiesmodel->get_all_varieties((string)$year, $type, $location_type, $location_code);
        $data['all_top3_varieties'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_varieties_data_by_region(){
        $filter = $this->input->post('filter');
        $radio_value = $this->input->post('radio_value');
        $year = $this->input->post('season');

        $location_code = $this->input->post('location_id');

        $location_type = '1';

        // Get location coordinates
        $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
        $data['location_coordinates'] = json_encode($temp_array);
        $temp_array = array();

        // ////////////////////////////////////////////////////////////////////// PRODUCTION CODE FOR REGION
        //maps section
        $temp_array = $this->seedsvarietiesmodel->get_varieties_geocoded_by_region(null, $location_type, (string)$year, null, '2', 'DESC', null, $location_code, $radio_value, $filter);
        // foreach ($temp_array as &$active_array){
        //     $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        // } 
        $data['provincial_varieties_geocoded'] = json_encode($temp_array);
        $temp_array = array();
        
        echo json_encode($data);
    }

    //rice farmers section
    public function get_socio_data_by_region(){
        $indicator = $this->input->post('indicator');
        $season = $this->input->post('season');
        $category = $this->input->post('category');
        $location_code = $this->input->post('location_id');

        $location_type = '1';
        $year = $season;

        // Get location coordinates
        $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
        $data['location_coordinates'] = json_encode($temp_array);
        $temp_array = array();

        // ////////////////////////////////////////////////////////////////////// PRODUCTION CODE FOR REGION
        //maps section
        $temp_array = $this->sociomodel->get_all_socio_totals_geocodes(null, $location_type, $year, null, '2', 'DESC', null, $location_code, $indicator);
        if($indicator == "AGE"){
            foreach ($temp_array as &$active_array){
                $active_array['value_age'] = number_format($active_array['value_age'], 0, '.', '');
            }
        }else if($indicator == "SEX"){
            foreach ($temp_array as &$active_array){
                $active_array['value_male'] = number_format($active_array['value_male'], 0, '.', '');
                $active_array['value_female'] = number_format($active_array['value_female'], 0, '.', '');
            }
        }
         
        $data['provincial_socio_geocoded'] = json_encode($temp_array);
        $temp_array = array();
        
        echo json_encode($data);
    }

    public function get_socio_data(){
        $indicator = $this->input->post('indicator');
        $season = $this->input->post('season');
        $category = $this->input->post('category');
        $location_code = $this->input->post('location_id');

        $location_type = '2';
        $year = $season;

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        ///////////////////////////////////////////////////////////////////////
        //maps section provincial
        $temp_array = array();
        if (isset($location_code)){
            $temp_array = $this->sociomodel->get_all_socio_totals_geocodes(null, $location_type, $year, null, '2', 'DESC', null, $location_code, $indicator);
        }else{
            $temp_array = $this->sociomodel->get_all_socio_totals_geocodes(null, $location_type, $year, null, '2', 'DESC', null, null, $indicator);
        }
        
        if($indicator == "AGE"){
            foreach ($temp_array as &$active_array){
                $active_array['value_age'] = number_format($active_array['value_age'], 0, '.', '');
            }
        }else if($indicator == "SEX"){
            foreach ($temp_array as &$active_array){
                $active_array['value_male'] = number_format($active_array['value_male'], 0, '.', '');
                $active_array['value_female'] = number_format($active_array['value_female'], 0, '.', '');
            }
        }
        
        $data['socio_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }


    public function get_socio_data_v2(){

        $indicator = $this->input->post('indicator');
        $season = $this->input->post('season');
        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');

        $year = $season;

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        ///////////////////////////////////////////////////////////////////////
        $temp_array = $this->sociomodel->get_all_socio_totals_geocodes_v2(null, $location_type, (string)$year, null, '2', 'DESC', null, $location_code, $indicator);
        
        if($indicator == "AGE"){
            foreach ($temp_array as &$active_array){
                $active_array['value_age'] = number_format($active_array['value_age'], 0, '.', '');
            }
        }else if($indicator == "SEX"){
            foreach ($temp_array as &$active_array){
                $active_array['value_male'] = number_format($active_array['value_male'], 0, '.', '');
                $active_array['value_female'] = number_format($active_array['value_female'], 0, '.', '');
            }
        }
        
        $data['socio_geocoded_v2'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

  


    public function get_householdincome_data_by_region(){
        $indicator = $this->input->post('indicator');
        $season = $this->input->post('season');
        // $category = $this->input->post('category');
        $location_code = $this->input->post('location_id');

        $location_type = '1';
        $year = $season;

        // Get location coordinates
        $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
        $data['location_coordinates'] = json_encode($temp_array);
        $temp_array = array();

        // ////////////////////////////////////////////////////////////////////// PRODUCTION CODE FOR REGION
        //maps section
        $temp_array = $this->householdmodel->get_all_householdincome_totals_geocodes(null, $location_type, $year, null, '2', 'DESC', null, $location_code, $indicator);
        // if($indicator == "EST_MONTHLY_INCOME"){
        //     foreach ($temp_array as &$active_array){
        //         $active_array['value_monthlyincome'] = number_format($active_array['value_monthlyincome'], 0, '.', '');
        //     }
        // }
        // else if($indicator == "POVERTY_THRES"){
        //     foreach ($temp_array as &$active_array){
        //         $active_array['value_above_povertythres'] = number_format($active_array['value_above_povertythres'], 0, '.', '');
        //     }
        // }
         
        $data['provincial_householdincome_geocoded'] = json_encode($temp_array);
        $temp_array = array();
        
        echo json_encode($data);
    }


    public function get_householdincome_data(){
        $indicator = $this->input->post('indicator');
        $season = $this->input->post('season');
        $location_code = $this->input->post('location_id');

        $location_type = '2';
        $year = $season;

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        ///////////////////////////////////////////////////////////////////////
        //maps section provincial
        $temp_array = array();
        if (isset($location_code)){
            $temp_array = $this->householdmodel->get_all_householdincome_totals_geocodes(null, $location_type, $year, null, '2', 'DESC', null, $location_code, $indicator);
        }else{
            $temp_array = $this->householdmodel->get_all_householdincome_totals_geocodes(null, $location_type, $year, null, '2', 'DESC', null, null, $indicator);
        }
        
        // if($indicator == "EST_MONTHLY_INCOME"){
        //     foreach ($temp_array as &$active_array){
        //         $active_array['value_monthlyincome'] = number_format($active_array['value_monthlyincome'], 0, '.', '');
        //     }
        // }
        
        $data['householdincome_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }



    public function get_householdincome_data_v2(){

        $indicator = $this->input->post('indicator');
        $season = $this->input->post('season');
        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');

        $year = $season;

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        ///////////////////////////////////////////////////////////////////////
        $temp_array = $this->householdmodel->get_all_householdincome_totals_geocodes_v2(null, $location_type, (string)$year, null, '2', 'DESC', null, $location_code, $indicator);
        
        
        $data['householdincome_geocoded_v2'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }


    public function get_household_income_data_by_region_by_province(){

        $indicator = $this->input->post('indicator');
        $season = $this->input->post('season');
        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');
        $year = $season;

        $orig_location_type = $this->input->post('orig_location_type');

        $temp_array = $this->locationmodel->get_parent_code($location_code, $orig_location_type);
        $parent_code = $temp_array['parent_code'];
        $data['parent_code'] = $parent_code;

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $orig_location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        ///////////////////////////////////////////////////////////////////////
        // $temp_array = $this->householdmodel->get_all_householdincome_totals_geocodes_v2(null, $location_type, (string)$year, null, '2', 'DESC', null, $parent_code, $indicator);
        $temp_array = $this->householdmodel->get_all_householdincome_totals_geocodes(null, $location_type, $year, null, '2', 'DESC', null, $parent_code, $indicator);
        $data['provincial_householdincome_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }


    public function get_farmcharacter_data_by_region(){
        $indicator = $this->input->post('indicator');
        $season = $this->input->post('season');
        // $category = $this->input->post('category');
        $location_code = $this->input->post('location_id');

        $location_type = '1';
        $year = $season;

        // Get location coordinates
        $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
        $data['location_coordinates'] = json_encode($temp_array);
        $temp_array = array();

        // ////////////////////////////////////////////////////////////////////// PRODUCTION CODE FOR REGION
        //maps section
        $temp_array = $this->farmcharacteristicsmodel->get_all_farmcharacter_totals_geocodes(null, $location_type, $year, null, '2', 'DESC', null, $location_code, $indicator);
         
        $data['provincial_farmchar_geocoded'] = json_encode($temp_array);
        $temp_array = array();
        
        echo json_encode($data);
    }


    public function get_farmcharacter_data(){
        $indicator = $this->input->post('indicator');
        $season = $this->input->post('season');
        $location_code = $this->input->post('location_id');

        $location_type = '2';
        $year = $season;

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        ///////////////////////////////////////////////////////////////////////
        //maps section provincial
        $temp_array = array();
        if (isset($location_code)){
            $temp_array = $this->farmcharacteristicsmodel->get_all_farmcharacter_totals_geocodes(null, $location_type, $year, null, '2', 'DESC', null, $location_code, $indicator);
        }else{
            $temp_array = $this->farmcharacteristicsmodel->get_all_farmcharacter_totals_geocodes(null, $location_type, $year, null, '2', 'DESC', null, null, $indicator);
        }
          
        $data['farmcharacter_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }


    public function get_farmcharacter_data_v2(){

        $indicator = $this->input->post('indicator');
        $season = $this->input->post('season');
        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');

        $year = $season;

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        ///////////////////////////////////////////////////////////////////////
        $temp_array = $this->farmcharacteristicsmodel->get_all_farmcharacter_totals_geocodes_v2(null, $location_type, (string)$year, null, '2', 'DESC', null, $location_code, $indicator);
        
        $data['farmcharacter_geocoded_v2'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }


    //others
    public function get_yieldarea_data(){
        
        $year = $this->input->post('year');
        $location_code = $this->input->post('location_id');
        $location_type = '2';
        
        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        ///////////////////////////////////////////////////////////////////////
        //maps section provincial
        $temp_array = array();
        if (isset($location_code)){
            $temp_array = $this->yieldmodel->get_yield_area_geocodes(null, $location_type, $year, null, '2', '1', '1', 'DESC', null, $location_code);
                                                                    
        }else{
            $temp_array = $this->yieldmodel->get_yield_area_geocodes(null, $location_type, $year, null, '2', '1', '1', 'DESC', null, null);                                                       
        }
        
        
     
        foreach ($temp_array as &$active_array){
            $active_array['value_yield'] = number_format($active_array['value_yield'], 2);
            $active_array['value_area'] = number_format($active_array['value_area'], 2, '.', '');
        } 
        
        
        $data['yieldarea_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_yield_cost_performance_data(){
        
        $year = $this->input->post('year');
        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');
        
        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type, $year);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }

        
        $temp_array = $this->yieldcostperformancemodel->get_yield_cost_performance_average_value_by_location('999', '2', $year);
        $data['yield_cost_performance_average_value'] = json_encode($temp_array);
        $temp_array = array();
        

        ///////////////////////////////////////////////////////////////////////
        //maps section provincial
       
        $temp_array = $this->yieldcostperformancemodel->get_yield_cost_performance_geocodes(null, $location_type, $year, null, '2', '1', '1', 'DESC', null, null);                                                       
        
        // Initialize sums and count for averaging
        $total_yield = 0;
        $total_cost = 0;
        $count = count($temp_array);

        foreach ($temp_array as &$active_array) {
            // Convert and format values
            $active_array['value_yield'] = $active_array['value_yield'];
            $active_array['value_cost'] = number_format($active_array['value_cost'], 2, '.', '');

            // Sum up values for averaging
            $total_yield += floatval($active_array['value_yield']);
            $total_cost += floatval($active_array['value_cost']);
        }

        // Compute averages if data exists
        $data['average_yield'] = ($count > 0) ? number_format($total_yield / $count, 2) : '0.00';
        $data['average_cost'] = ($count > 0) ? number_format($total_cost / $count, 0, '.', '') : '0.00';
        
        $data['yield_cost_performance_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_yield_cost_performance_data_region(){
        
        $year = $this->input->post('year');
        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');
        
        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }

        $temp_array = $this->yieldcostperformancemodel->get_yield_cost_performance_average_value_by_location('999', '2', $year);
        $data['yield_cost_performance_average_value'] = json_encode($temp_array);
        $temp_array = array();
        
        $temp_array = $this->yieldcostperformancemodel->get_yield_cost_performance_geocodes(null, '2', $year, null, '2', '1', '1', 'DESC', null, null);  
        
        // Initialize sums and count for averaging
        $total_yield = 0;
        $total_cost = 0;
        $count = count($temp_array);

        foreach ($temp_array as &$active_array) {
            // Convert and format values
            $active_array['value_yield'] = $active_array['value_yield'];
            $active_array['value_cost'] = number_format($active_array['value_cost'], 2, '.', '');

            // Sum up values for averaging
            $total_yield += floatval($active_array['value_yield']);
            $total_cost += floatval($active_array['value_cost']);
        }

        // Compute averages if data exists
        $data['average_yield'] = ($count > 0) ? number_format($total_yield / $count, 2) : '0.00';
        $data['average_cost'] = ($count > 0) ? number_format($total_cost / $count, 0, '.', '') : '0.00';
        $temp_array = array();

        $temp_array = $this->yieldcostperformancemodel->get_yield_cost_performance_geocodes(null, $location_type, $year, null, '2', '1', '1', 'DESC', null, $location_code);
        $data['yield_cost_performance_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }


    public function get_source_of_income(){
        $year = $this->input->post('season');
        $indicator = $this->input->post('indicator'); 

        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        //maps section provincial
        $temp_array = $this->sourceofincomemodel->get_source_of_income_geocoded(null, $location_type, (string)$year, null, '2', 'ASC', null, $indicator);
        $data['source_of_income_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }


    public function get_source_of_income_by_region(){
        $year = $this->input->post('season');
        $indicator = $this->input->post('indicator');
        $location_code = $this->input->post('location_id');

        $location_type = '1';

        // Get location coordinates
        $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
        $data['location_coordinates'] = json_encode($temp_array);
        $temp_array = array();

        // ////////////////////////////////////////////////////////////////////// PRODUCTION CODE FOR REGION
        //maps section
        $temp_array = $this->sourceofincomemodel->get_source_of_income_by_region(null, $location_type, $year, null, '2', 'DESC', null, $location_code, $indicator);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['provincial_source_of_income_geocoded'] = json_encode($temp_array);
        $temp_array = array();
        
        echo json_encode($data);
    }

    public function get_digital_proficiency(){
        $year = $this->input->post('season');
        $indicator = $this->input->post('indicator'); 

        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        //maps section provincial
        $temp_array = $this->digitalproficiencymodel->get_digital_proficiency_geocoded(null, $location_type, (string)$year, null, '2', 'ASC', null, $indicator);
        $data['digital_proficiency_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }


    public function get_digital_proficiency_by_region(){
        $year = $this->input->post('season');
        $indicator = $this->input->post('indicator');
        $location_code = $this->input->post('location_id');

        $location_type = '1';

        // Get location coordinates
        $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
        $data['location_coordinates'] = json_encode($temp_array);
        $temp_array = array();

        // ////////////////////////////////////////////////////////////////////// PRODUCTION CODE FOR REGION
        //maps section
        $temp_array = $this->digitalproficiencymodel->get_digital_proficiency_by_region(null, $location_type, $year, null, '2', 'DESC', null, $location_code, $indicator);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['provincial_digital_proficiency_geocoded'] = json_encode($temp_array);
        $temp_array = array();
        
        echo json_encode($data);
    }

    public function get_productivity(){
        $year = $this->input->post('season');

        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        //maps section provincial
        $temp_array = $this->productivitymodel->get_productivity_geocoded(null, $location_type, (string)$year, null, null, 'ASC', null);
        foreach ($temp_array as &$active_array) {
            $active_array['value'] = number_format($active_array['value'] / 1000, 2, '.', '');
        } 
        $data['productivity_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }


    public function get_productivity_by_region(){
        $year = $this->input->post('season');
        $location_code = $this->input->post('location_id');

        $location_type = '1';

        // Get location coordinates
        $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
        $data['location_coordinates'] = json_encode($temp_array);
        $temp_array = array();

        // ////////////////////////////////////////////////////////////////////// PRODUCTION CODE FOR REGION
        //maps section
        $temp_array = $this->productivitymodel->get_productivity_by_region(null, $location_type, $year, null, null, 'DESC', null, $location_code);
        foreach ($temp_array as &$active_array) {
            $active_array['value'] = number_format($active_array['value'] / 1000, 2, '.', '');
        } 
        $data['provincial_productivity_geocoded'] = json_encode($temp_array);
        $temp_array = array();
        
        echo json_encode($data);
    }


    public function get_profitability(){
        $year = $this->input->post('season');
        $indicator = $this->input->post('indicator'); 

        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        //maps section provincial
        $temp_array = $this->profitabilitymodel->get_profitability_geocoded(null, $location_type, (string)$year, null, null, 'ASC', null, $indicator);
        $data['profitability_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }


    public function get_profitability_by_region(){
        $year = $this->input->post('season');
        $indicator = $this->input->post('indicator');
        $location_code = $this->input->post('location_id');

        $location_type = '1';

        // Get location coordinates
        $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
        $data['location_coordinates'] = json_encode($temp_array);
        $temp_array = array();

        // ////////////////////////////////////////////////////////////////////// PRODUCTION CODE FOR REGION
        //maps section
        $temp_array = $this->profitabilitymodel->get_profitability_by_region(null, $location_type, $year, null, null, 'DESC', null, $location_code, $indicator);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['provincial_profitability_geocoded'] = json_encode($temp_array);
        $temp_array = array();
        
        echo json_encode($data);
    }


    // GET SOURCE OF POWER

    public function get_source_of_power_data(){
        $source_of_power = $this->input->post('source_of_power');
        $year = $this->input->post('season');
        $category = $this->input->post('category');

        $location_code = $this->input->post('location_id');
        $location_type = $this->input->post('location_type');

        if (isset($location_code)){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        ///////////////////////////////////////////////////////////////////////
        //maps section provincial
        $temp_array = $this->sourceofpowermodel->get_source_of_power_geocoded(null, $location_type, (string)$year, null, '2', 'ASC', null, (int)$category, $source_of_power);
        $data['source_of_power_geocoded'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_source_of_power_data_by_region(){
        $source_of_power = $this->input->post('source_of_power');
        $season = $this->input->post('season');
        $category = $this->input->post('category');
        $location_code = $this->input->post('location_id');

        $location_type = '1';
        $year = $season;

        // Get location coordinates
        $temp_array = $this->locationmodel->get_coordinates($location_code, $location_type);
        $data['location_coordinates'] = json_encode($temp_array);
        $temp_array = array();

        // ////////////////////////////////////////////////////////////////////// Source of power CODE FOR REGION
        //maps section
        $temp_array = $this->sourceofpowermodel->get_source_of_power_by_region(null, $location_type, $year, null, '2', 'DESC', null, $location_code, (int)$category, $source_of_power);
        foreach ($temp_array as &$active_array){
            $active_array['value'] = number_format($active_array['value'], 0, '.', '');
        } 
        $data['provincial_source_of_power_geocoded'] = json_encode($temp_array);
        $temp_array = array();
        
        echo json_encode($data);
    }

    public function get_location_by_loc_and_category(){
        $category = $this->input->post('category');
        $location_code = $this->input->post('location_code');
        $location_type = $this->input->post('location_type');
       
        // //////////////////////////////////////////////////////////////////////
        //maps section
        $temp_array = $this->mainmodel->get_location_by_loc_and_category($category, $location_code, $location_type);
        $data['is_location'] = json_encode($temp_array);
        $temp_array = array();
        
        echo json_encode($data);
    }

    public function get_rb_count(){
        $year = $this->input->post('year');
        $locType = $this->input->post('location_type');
        $locCode = $this->input->post('location_code');
        
        ///////////////////////////////////////////////////////////////////////
        $temp_array = $this->mainmodel->get_rb_count($year, $locCode, $locType);
        $data['n_count'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);
    }

    public function get_inflation_data(){
        $year = $this->input->post('year');
        $loc_type = $this->input->post('loc_type');
        $loc_code = $this->input->post('loc_code');
        $month = $this->input->post('month'); // 1 to 12, 13 is annual
        $metric = $this->input->post('metric'); // rice inflation, headline inflation

        $map_boundary = $this->input->post('map_boundary');

        if ($map_boundary == 'REGION' && $loc_code == "999"){
            $loc_type = '1';
            $loc_code = NULL;
        } else if($map_boundary == 'PROVINCE' && $loc_code == "999"){
            $loc_type = '2';
            $loc_code = NULL;
        }

        // $loctype = '1';
        // $loccode = NULL;

        if ($loc_type && $loc_code){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($loc_code, $loc_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();

        }

        // To get all provinces
        if ($loc_type == '2' && $loc_code != '999'){
            $loc_type = '2';
            $loc_code = NULL;
        }

        if ($metric == "headline") {
            ///////////////////////////////////////////////////////////////////////// HEADLINE INFLATION CODE
    
            $temp_array = $this->inflationmodel->get_headline_geocodes($loc_code, $loc_type, $year, null, null, $month);
            foreach ($temp_array as &$active_array) {
                $active_array['value'] = number_format($active_array['value'] ?? 0, 2, '.', ''); // Replace null with 0
            } 
            $data['inflation_geocoded'] = json_encode($temp_array);
            $temp_array = array();
        } else {
            /////////////////////////////////////////////////////////////////////// RICE INFLATION CODE

            $temp_array = $this->inflationmodel->get_inflation_geocodes($loc_code, $loc_type, $year, null, null, $month);
            foreach ($temp_array as &$active_array) {
                $active_array['value'] = number_format($active_array['value'] ?? 0, 2, '.', ''); // Replace null with 0
                $active_array['contri'] = number_format($active_array['contri'] ?? 0, 2, '.', '');
            } 
            $data['inflation_geocoded'] = json_encode($temp_array);
            $temp_array = array();
        }


        echo json_encode($data);
    }

    public function get_inflation_data_by_region(){
        $year = $this->input->post('year');
        $loc_type = $this->input->post('loc_type');
        $loc_code = $this->input->post('loc_code');
        $month = $this->input->post('month'); // 1 to 12, 13 is annual
        $metric = $this->input->post('metric'); // rice inflation, headline inflation
        
        // $loctype = '1';
        // $loccode = NULL;

        if ($loc_type && $loc_code){
            // Get location coordinates
            $temp_array = $this->locationmodel->get_coordinates($loc_code, $loc_type);
            $data['location_coordinates'] = json_encode($temp_array);
            $temp_array = array();

        }

        if ($metric == "rice"){
            $temp_array = $this->inflationmodel->get_inflation_geocodes_provinces(null, '1', $year, null, null, $month);
            $data['inflation_province_geocoded'] = json_encode($temp_array);
            $temp_array = array();
        } else { // if headline
            $temp_array = $this->inflationmodel->get_headline_geocodes_provinces(null, '1', $year, null, null, $month);
            $data['inflation_province_geocoded'] = json_encode($temp_array);
            $temp_array = array();
        }
        
        echo json_encode($data);
    }

    public function get_latest_24_inflation_data(){
        $period = $this->input->post('period');

        // GET ALL SOCIO GEOCODED PER PROVINCE
        $temp_array = $this->inflationmodel->get_latest_24_inflation_data($period);
        $data['latest_inflation_data'] = json_encode($temp_array);
        $temp_array = array();

        echo json_encode($data);        
    }

    public function get_available_month(){
        
        $table = $this->input->post('table');
        $year = $this->input->post('year');
        $month = $this->input->post('month');
    
        if (!empty($table)) {
            $temp_array = $this->mainmodel->get_month_data($table, $year, $month, 'ASC');

            // Extract just the year values from the array
            $months = array_column($temp_array, 'month');
    
            // Return years as JSON
            $data['months'] = $months;
            echo json_encode($data);
        } else {
            // Return an error if the table is not set
            echo json_encode(['error' => 'Invalid category']);
        }
    }
}
