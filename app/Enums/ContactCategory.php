<?php

namespace App\Enums;

enum ContactCategory: string
{
    case General = 'General';
    case Support = 'Support';
    case Feedback = 'Feedback';
    case Security = 'Security';
    case MediaPress = 'Media/Press';
    case BusinessInquiry = 'Business Inquiry';
    case ShopOrders = 'Shop & Orders';
    case Legal = 'Legal';
    case FeatureRequest = 'Feature Request';
}