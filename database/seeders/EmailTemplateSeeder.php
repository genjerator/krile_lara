<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Initial Contact',
                'subject' => 'Partnership Opportunity with {{company_name}}',
                'body_html' => '<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .footer { background-color: #f1f1f1; padding: 10px; text-align: center; font-size: 12px; color: #666; }
        .button { background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; display: inline-block; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Hello {{company_name}}!</h1>
    </div>
    <div class="content">
        <p>We hope this email finds you well.</p>
        <p>We came across your company based in <strong>{{city}}</strong> and were impressed by your work in the <strong>{{category}}</strong> industry.</p>
        <p>We would love to explore potential partnership opportunities that could benefit both our organizations.</p>
        <p>Would you be interested in a brief call to discuss this further?</p>
        <p style="text-align: center; margin: 30px 0;">
            <a href="mailto:{{email}}" class="button">Reply to This Email</a>
        </p>
        <p>Looking forward to hearing from you!</p>
    </div>
    <div class="footer">
        <p>Best regards,<br>Your Company Team</p>
        <p>This email was sent to {{email}}</p>
    </div>
</body>
</html>',
                'body_text' => 'Hello {{company_name}}!

We hope this email finds you well.

We came across your company based in {{city}} and were impressed by your work in the {{category}} industry.

We would love to explore potential partnership opportunities that could benefit both our organizations.

Would you be interested in a brief call to discuss this further?

Looking forward to hearing from you!

Best regards,
Your Company Team',
                'is_active' => true,
            ],
            [
                'name' => 'Promotional Offer',
                'subject' => 'Special Offer for {{company_name}}',
                'body_html' => '<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .banner { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px; text-align: center; }
        .content { padding: 30px; }
        .offer-box { background-color: #f8f9fa; border: 2px dashed #667eea; padding: 20px; margin: 20px 0; text-align: center; }
        .cta-button { background-color: #667eea; color: white; padding: 15px 30px; text-decoration: none; display: inline-block; border-radius: 5px; font-weight: bold; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="banner">
        <h1>🎉 Exclusive Offer for {{company_name}}!</h1>
    </div>
    <div class="content">
        <p>Dear {{company_name}} team in {{city}},</p>
        <p>As a valued business in the {{category}} sector, we are excited to offer you an exclusive opportunity!</p>
        <div class="offer-box">
            <h2 style="color: #667eea; margin-top: 0;">LIMITED TIME OFFER</h2>
            <p style="font-size: 18px; margin: 20px 0;"><strong>Get 30% OFF on all our services!</strong></p>
            <p>Valid for the next 7 days</p>
        </div>
        <p>This special offer is designed specifically for businesses like yours in {{city}}.</p>
        <p style="text-align: center;">
            <a href="{{website}}" class="cta-button">Claim Your Offer Now</a>
        </p>
        <p>If you have any questions, feel free to contact us at {{email}} or call {{phone}}.</p>
        <p>Don\'t miss out on this opportunity!</p>
        <p>Best regards,<br>Your Marketing Team</p>
    </div>
</body>
</html>',
                'body_text' => 'Exclusive Offer for {{company_name}}!

Dear {{company_name}} team in {{city}},

As a valued business in the {{category}} sector, we are excited to offer you an exclusive opportunity!

LIMITED TIME OFFER
Get 30% OFF on all our services!
Valid for the next 7 days

This special offer is designed specifically for businesses like yours in {{city}}.

Visit: {{website}}

If you have any questions, feel free to contact us at {{email}} or call {{phone}}.

Don\'t miss out on this opportunity!

Best regards,
Your Marketing Team',
                'is_active' => true,
            ],
            [
                'name' => 'Newsletter',
                'subject' => 'Industry Update for {{category}} Businesses',
                'body_html' => '<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .header { background-color: #2c3e50; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; max-width: 600px; margin: 0 auto; }
        .article { border-bottom: 1px solid #eee; padding: 15px 0; }
        .footer { background-color: #ecf0f1; padding: 20px; text-align: center; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📰 Industry Newsletter</h1>
        <p>Tailored for {{category}} Businesses</p>
    </div>
    <div class="content">
        <p>Hello {{company_name}},</p>
        <p>Welcome to this month\'s industry update, specially curated for {{category}} businesses in {{city}}.</p>

        <div class="article">
            <h3>📈 Industry Trends</h3>
            <p>Stay ahead of the curve with the latest developments in your sector.</p>
        </div>

        <div class="article">
            <h3>💡 Business Tips</h3>
            <p>Practical advice to help grow your business and improve efficiency.</p>
        </div>

        <div class="article">
            <h3>🤝 Networking Opportunities</h3>
            <p>Connect with other businesses in {{city}} and expand your network.</p>
        </div>

        <p style="margin-top: 30px;">Thank you for being a valued member of our community!</p>
        <p>Visit your website: {{website}}</p>
    </div>
    <div class="footer">
        <p>Contact us: {{email}} | {{phone}}<br>
        Located in {{city}} - {{street}}, {{postal_code}}</p>
    </div>
</body>
</html>',
                'body_text' => 'Industry Newsletter - Tailored for {{category}} Businesses

Hello {{company_name}},

Welcome to this month\'s industry update, specially curated for {{category}} businesses in {{city}}.

INDUSTRY TRENDS
Stay ahead of the curve with the latest developments in your sector.

BUSINESS TIPS
Practical advice to help grow your business and improve efficiency.

NETWORKING OPPORTUNITIES
Connect with other businesses in {{city}} and expand your network.

Thank you for being a valued member of our community!

Visit your website: {{website}}

Contact us: {{email}} | {{phone}}
Located in {{city}} - {{street}}, {{postal_code}}',
                'is_active' => false, // Inactive example
            ],
        ];

        foreach ($templates as $templateData) {
            EmailTemplate::create($templateData);
        }

        $this->command->info('Created ' . count($templates) . ' email templates.');
    }
}
