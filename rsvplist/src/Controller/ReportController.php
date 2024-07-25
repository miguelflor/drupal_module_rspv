<?php

/**
 * @file
 * Provide site administratos with a list of all the RSVP List signups
 * so they know is attending their events
 */

 namespace drupal\rsvplist\Controller;

 use Drupal\Core\Controller\ControllerBase;
 use Drupal\Core\Database\Database;

 class ReportController extends ControllerBase {

    /**
     * @return array|null
     */
    protected function load() {
        try {

            $database = \Drupal::database();
            $select_query = $database->select('rsvplist','r');

            $select_query->join('users_field_data', 'u', 'r.uid = u.uid');
            $select_query->join('node_field_data','n','r.nid = n.nid');
            $select_query->addField('u','name','username');
            $select_query->addField('n','title');
            $select_query->addField('r', 'mail');
            
            $entries = $select_query->execute()->fetchAll(\PDO::FETCH_ASSOC);
            return $entries;
        }
        catch (\Exception $e) {
            \Drupal::messenger()->addStatus(
                t('Unable to access the database at this time. please try again later.')
            );
            return NULL;
        }
    }

    /**
     * @return array
     */
    public function report() {
        $content = [];

        $content['message'] = [
            '#markup' => t('Below is a list of all Event RSVPs including username,
            email address and the name of the event they will be attending.'),
        ];

        $headers = [
            t('Username'),
            t('Event'),
            t('Email'),
        ];

        $table_rows = $this->load();

        $content['table'] = [
            '#type' => 'table',
            '#header' => $headers,
            '#rows' => $table_rows,
            '#empty' => t('No entries available.'),
        ];

        $content['#cache']['max-age'] = 0;

        return $content;
    }
 }