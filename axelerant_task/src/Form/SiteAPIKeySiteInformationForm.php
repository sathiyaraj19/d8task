<?php
namespace Drupal\axelerant_task\Form;

// Classes referenced in this class:
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;

// This is the form we are extending
use Drupal\system\Form\SiteInformationForm;

/**
 * Configure site information settings for this site.
 */
class SiteAPIKeySiteInformationForm extends SiteInformationForm
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        // Retrieve the system.site configuration
        $site_config = $this->config('system.site');

        // Get the original form from the class we are extending
        $form = parent::buildForm($form, $form_state);

        // Add a textarea to the site information section of the form for our
        // site API key
        $form['site_information']['site_api_key'] = [
          '#type' => 'textfield',
          '#title' => t('Site API Key'),
          '#default_value' => $site_config->get('site_api_key_value') != '' ? $site_config->get('site_api_key_value') :'No API Key yet',
          '#description' => $this->t('The API Key of the site'),
        ];
        $form['actions']['submit']['#value'] = t('Update Configuration');

    
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // Now we need to save the new site api key to the
        // system.site.site_api_key configuration.
        $this->config('system.site')
            // The site_api_key is retrieved from the submitted form values
            // and saved to the 'site_api_key_value' element of the system.site configuration
            ->set('site_api_key_value', $form_state->getValue('site_api_key'))
            // Make sure to save the configuration
            ->save();

        // Pass the remaining values off to the original form that we have extended,
        // so that they are also saved
        parent::submitForm($form, $form_state);
    }
}
?>