<?php
if (!defined('ABSPATH'))
    exit;

/**
 * @class 		MVX Product QNA-
 *
 * @version		3.0.0
 * @package		MultivendorX
 * @author 		MultiVendorX
 */
class MVX_Product_QNA {

    private $question_table;
    private $answer_table;

    public function __construct() {
        global $wpdb;
        $this->question_table = $wpdb->prefix.'mvx_cust_questions';
        $this->answer_table = $wpdb->prefix.'mvx_cust_answers';
        add_action( 'mvx_product_qna_delete_question', array($this, 'mvx_product_qna_delete_question'));
    }
    
    /**
     * Method to create a new question.
     *
     * @since 3.0.0
     * @param MVX_Product_QNA question $data
     */
    public function createQuestion( $data ) {
        global $wpdb;
        $data = apply_filters( 'mvx_product_qna_insert_question', $data );
        $wpdb->insert( $this->question_table, $data );
        return $wpdb->insert_id;
    }

    /**
     * Update question in the database.
     *
     * @since 3.0.0
     * @param MVX_Product_QNA question $ques_ID
     * @param MVX_Product_QNA question $data
     */
    public function updateQuestion( $ques_ID, $data ) {
        global $wpdb;
        if ( $ques_ID ) {
            $data = apply_filters( 'mvx_product_qna_update_question', $data, $ques_ID );
            return $wpdb->update( $this->question_table, $data, array( 'ques_ID' => $ques_ID ) );
        }
    }

    /**
     * Delete a question from the database.
     *
     * @since  3.0.0
     * @param MVX_Product_QNA question $ques_ID
     */
    public function deleteQuestion( $ques_ID ) {
        if ( $ques_ID ) {
            global $wpdb;
            $wpdb->delete( $this->question_table, array( 'ques_ID' => $ques_ID ) );
            do_action( 'mvx_product_qna_delete_question', $ques_ID );
        }
    }
    
    /**
     * Delete all answers of a question from database.
     *
     * @since  3.0.4
     * @param MVX_Product_QNA question $ques_ID
     */
    public function mvx_product_qna_delete_question( $ques_ID ) {
        if ($ques_ID) {
            $answers = $this->get_Answers($ques_ID);
            if ($answers) {
                foreach ($answers as $ans) {
                    $this->deleteAnswer($ans->ans_ID);
                }
            }
        }
    }

    /**
     * Get a question.
     *
     * @since  3.0.0
     * @param  int   $ques_ID      Question ID
     * @return object               objects question
     */
    public function get_Question( $ques_ID ) {
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->question_table} WHERE ques_ID = %d ", $ques_ID ) );
    }

    public function get_Pending_Questions( $product_ID ) {
        $questions = $this->get_Questions( $product_ID );
        $pending_questions = [];
        foreach( $questions as $question ) {
            if ( $question->status == 'pending' ) {
                $pending_questions[] = $question;
            } 
        }
        $pending_questions = !empty($pending_questions)?$pending_questions:'';
        return $pending_questions;
    }

    /**
     * Get a list of questions for a product.
     *
     * @since  3.0.0
     * @param  int   $product_ID      Product ID
     * @return array               Array of objects questions
     */
    public function get_Questions( $product_ID, $where = '' ) {
        global $wpdb;
        return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$this->question_table} WHERE product_ID = %d ". esc_sql($where) ."" , $product_ID ) );
    }
    
    /**
     * Get a list of questions for vendor products.
     *
     * @since  3.0.0
     * @param  object   $vendor      Vendor object
     * @return array               Array of objects questions
     */
    public function get_Vendor_Questions( $vendor, $unanswer = true ) {
        $vendor_questions = array();
        if ($vendor && $vendor->get_products_ids()) { 
            foreach ($vendor->get_products_ids() as $product) { 
                $product_questions = $this->get_Questions($product->ID, "ORDER BY ques_created DESC");
                if ($product_questions) {
                    foreach ($product_questions as $question) {
                        if ($unanswer) {
                            $_is_answer_given = $this->get_Answers($question->ques_ID);
                            if (!$_is_answer_given) {
                                $vendor_questions[$question->ques_ID] = $question;
                            }
                        }else{
                            $vendor_questions[$question->ques_ID] = $question;
                        }
                    }
                }  
            }
        }
        return $vendor_questions;
    }

    public function get_All_Vendor_Questions( $unanswer = true ) {
        $vendor_questions = array();
        $args_multi_vendor = array(
            'posts_per_page' => -1,
            'post_type' => 'product',
            'post_status' => 'publish',
            'fields' => 'ids'
        );
        $vendor_query = new WP_Query($args_multi_vendor);
        if ($vendor_query->get_posts()) { 
            foreach ($vendor_query->get_posts() as $product) { 
                $product_questions = $this->get_Questions($product, "ORDER BY ques_created DESC");
                if ($product_questions) {
                    foreach ($product_questions as $question) {
                        if ($unanswer) {
                            $_is_answer_given = $this->get_Answers($question->ques_ID);
                            if (!$_is_answer_given) {
                                $vendor_questions[$question->ques_ID] = $question;
                            }
                        }else{
                            $vendor_questions[$question->ques_ID] = $question;
                        }
                    }
                }  
            }
        }
        return $vendor_questions;
    }
    
    /**
     * Method to create a new answer.
     *
     * @since 3.0.0
     * @param MVX_Product_QNA answer $data
     */
    public function createAnswer( $data ) {
        global $wpdb;
        $data = apply_filters( 'mvx_product_qna_insert_answer', $data );
        $wpdb->insert( $this->answer_table, $data );
        return $wpdb->insert_id;
    }

    /**
     * Update answer in the database.
     *
     * @since 3.0.0
     * @param MVX_Product_QNA answer $ans_ID
     * @param MVX_Product_QNA answer $data
     */
    public function updateAnswer( $ans_ID, $data ) {
        global $wpdb;
        if ( $ans_ID ) {
            $data = apply_filters( 'mvx_product_qna_update_answer', $data, $ans_ID );
            return $wpdb->update( $this->answer_table, $data, array( 'ans_ID' => $ans_ID) );
        }
    }

    /**
     * Delete a answer from the database.
     *
     * @since  3.0.0
     * @param MVX_Product_QNA answer $ans_ID
     */
    public function deleteAnswer( $ans_ID ) {
        if ( $ans_ID ) {
            global $wpdb;
            $wpdb->delete( $this->answer_table, array( 'ans_ID' => $ans_ID ) );
            do_action( 'mvx_product_qna_delete_answer', $ans_ID );
        }
    }
    
    /**
     * Get a answer.
     *
     * @since  3.0.0
     * @param  int   $ans_ID      Answer ID
     * @return object               objects answer
     */
    public function get_Answer( $ans_ID ) {
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->answer_table} WHERE ans_ID = %d ", $ans_ID ) );
    }

    /**
     * Get a list of Answers.
     *
     * @since  3.0.0
     * @param  int   $ques_ID      Question ID
     * @return array               Array of objects questions
     */
    public function get_Answers( $ques_ID = 0, $where = '' ) {
        global $wpdb;
        $get_ans_sql = "SELECT * FROM {$this->answer_table}";
        if ($ques_ID && $ques_ID != 0) {
            $get_ans_sql .=  " WHERE ques_ID = '" . esc_sql( $ques_ID ) . "' ";
        } 
        if ($where) {
            $get_ans_sql .= $where;
        }
        return $wpdb->get_results( $get_ans_sql );
    }
    
    /**
     * Get question answer list for a product.
     *
     * @since  3.0.0
     * @param  int   $product_ID      product ID
     * @return array               Array of objects questions
     */
    public function get_Product_QNA( $product_ID, $args = '' ) {
        global $wpdb;
        $default = array(
            'sortby'    => 'date',
            'sort'      => 'DESC',
            'where'     => ''
        );
        $args = wp_parse_args($args, $default);
        $get_qna_sql = "SELECT * FROM {$this->question_table} AS question INNER JOIN {$this->answer_table} AS answer ON question.ques_ID = answer.ques_ID WHERE product_ID = %d ";
        if ($args['sortby'] == 'date') {
            $get_qna_sql .= "ORDER BY question.ques_created ". esc_sql($args['sort']) ." ";
        }
        if ($args['where']) {
            $get_qna_sql .= $args['where'];
        }
        $product_QNAs = $wpdb->get_results( $wpdb->prepare( wc_clean($get_qna_sql), absint($product_ID) ) );
        if ($args['sortby'] == 'vote' && $product_QNAs) {
            $votes = array();
            foreach ($product_QNAs as $key => $qna) { 
                $count = 0;
                $ans_vote = maybe_unserialize($qna->ans_vote);
                if (is_array($ans_vote)) {
                    $count = array_sum($ans_vote);
                }
                $product_QNAs[$key]->vote_count = $count;
                $votes[$key] = $count;
            }

            if ($args['sort']== 'ASC') {
                array_multisort($votes, SORT_ASC, $product_QNAs);
            }else{
                array_multisort($votes, SORT_DESC, $product_QNAs);
            }
            return $product_QNAs;
        }else{
            return $product_QNAs;
        }
    }

}
