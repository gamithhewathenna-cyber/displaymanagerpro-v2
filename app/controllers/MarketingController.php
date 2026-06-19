<?php
/**
 * Marketing Controller – public website pages
 */
class MarketingController extends BaseController
{
    public function home(): void
    {
        $plans = Plan::active();
        $this->view('marketing/home', ['title' => 'Digital Signage Made Simple', 'plans' => $plans], 'marketing');
    }

    public function features(): void
    {
        $this->view('marketing/features', ['title' => 'Features'], 'marketing');
    }

    public function pricing(): void
    {
        $plans = Plan::active();
        $this->view('marketing/pricing', ['title' => 'Pricing', 'plans' => $plans], 'marketing');
    }

    public function industries(): void
    {
        $this->view('marketing/industries', ['title' => 'Industries'], 'marketing');
    }

    public function faq(): void
    {
        $this->view('marketing/faq', ['title' => 'FAQ'], 'marketing');
    }

    public function privacy(): void
    {
        $content = [
            'title'        => ContentController::get('privacy', 'title', 'Privacy Policy'),
            'last_updated' => ContentController::get('privacy', 'last_updated', ''),
            'body'         => ContentController::get('privacy', 'body', ''),
        ];
        $this->view('marketing/privacy', ['title' => $content['title'], 'content' => $content], 'marketing');
    }

    public function terms(): void
    {
        $content = [
            'title'        => ContentController::get('terms', 'title', 'Terms & Conditions'),
            'last_updated' => ContentController::get('terms', 'last_updated', ''),
            'body'         => ContentController::get('terms', 'body', ''),
        ];
        $this->view('marketing/terms', ['title' => $content['title'], 'content' => $content], 'marketing');
    }

    public function refund(): void
    {
        $content = [
            'title'        => ContentController::get('refund', 'title', 'Refund Policy'),
            'last_updated' => ContentController::get('refund', 'last_updated', ''),
            'body'         => ContentController::get('refund', 'body', ''),
        ];
        $this->view('marketing/refund', ['title' => $content['title'], 'content' => $content], 'marketing');
    }

    public function contact(): void
    {
        $this->view('marketing/contact', ['title' => 'Contact'], 'marketing');
    }

    public function contactSubmit(): void
    {
        $this->validateCsrf();
        $name    = Helpers::sanitize($_POST['name'] ?? '');
        $email   = Helpers::sanitize($_POST['email'] ?? '');
        $message = Helpers::sanitize($_POST['message'] ?? '');

        if (!$name || !Helpers::validateEmail($email) || strlen($message) < 10) {
            Session::flash('error', 'Please fill out all fields correctly.');
            $this->redirect('/contact');
        }

        $adminEmail = Settings::get('smtp_from_email', 'admin@localhost');
        $body = "<p><strong>From:</strong> $name ($email)</p><p><strong>Message:</strong><br>" . nl2br($message) . "</p>";
        Mailer::send($adminEmail, 'Admin', "Website Contact from $name", $body);

        Session::flash('success', 'Message sent! We\'ll be in touch within 1 business day.');
        $this->redirect('/contact');
    }

}
