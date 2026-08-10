<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Push;

/**
 * Every push code documented under `.shopee-docs/Push Mechanism/`, keyed
 * by each doc's own "Push Code" header value — the authoritative source.
 *
 * A handful of docs' own "Push Content" JSON examples show a different,
 * stale `code` than their header: `booking_status_push`'s example shows
 * `3` instead of `23`, `booking_trackingno_push`'s shows `4` instead of
 * `24`, `booking_shipping_document_status_push`'s shows `15` instead of
 * `25`, and `video_upload_result_push`'s shows `37` instead of `38` — each
 * collides with a different, unrelated push type's real code, which is a
 * strong sign of a copy-pasted example rather than the genuine value. The
 * header value is used here, the opposite of this SDK's usual "trust the
 * example over the header" policy for REST endpoint paths — that policy
 * exists because a wrong *path* fails signing outright and is therefore
 * falsifiable; an in-body `code` sample carries no such signal, so the
 * header (present and consistent across every doc, and cross-checked
 * against `.shopee-docs/Developer Guide/Getting Started/push_mechanism_notifications.md`
 * for the codes it covers) is the more reliable source here.
 *
 * Codes 6, 14, 17, 26, 32, and 39-46 are not documented under any of the 8
 * push categories this SDK covers (Shopee, Order, Marketing, Product,
 * Return, Fulfillment by Shopee, Webchat, Consignment Service). If Shopee
 * sends one of these — or any future code — {@see PushEvent::eventCode()}
 * returns `null`; {@see PushEvent::$code} still holds the raw integer.
 *
 * @see .shopee-docs/Push Mechanism/
 * @see .docs/push/README.md
 */
enum PushEventCode: int
{
    /** @see .shopee-docs/Push Mechanism/Shopee Push/shop_authorization_push.md */
    case ShopAuthorization = 1;

    /** @see .shopee-docs/Push Mechanism/Shopee Push/shop_authorization_canceled_push.md */
    case ShopAuthorizationCanceled = 2;

    /** @see .shopee-docs/Push Mechanism/Order Push/order_status_push.md */
    case OrderStatus = 3;

    /** @see .shopee-docs/Push Mechanism/Order Push/order_trackingno_push.md */
    case OrderTrackingNumber = 4;

    /** @see .shopee-docs/Push Mechanism/Shopee Push/shopee_updates.md */
    case ShopeeUpdates = 5;

    /** @see .shopee-docs/Push Mechanism/Marketing Push/item_promotion_push.md */
    case ItemPromotion = 7;

    /** @see .shopee-docs/Push Mechanism/Product Push/reserved_stock_change_push.md */
    case ReservedStockChange = 8;

    /** @see .shopee-docs/Push Mechanism/Marketing Push/promotion_update_push.md */
    case PromotionUpdate = 9;

    /** @see .shopee-docs/Push Mechanism/Webchat Push/webchat_push.md */
    case Webchat = 10;

    /** @see .shopee-docs/Push Mechanism/Product Push/video_upload_push.md */
    case VideoUpload = 11;

    /** @see .shopee-docs/Push Mechanism/Shopee Push/open_api_authorization_expiry.md */
    case OpenApiAuthorizationExpiry = 12;

    /** @see .shopee-docs/Push Mechanism/Product Push/brand_register_result.md */
    case BrandRegisterResult = 13;

    /** @see .shopee-docs/Push Mechanism/Order Push/shipping_document_status_push.md */
    case ShippingDocumentStatus = 15;

    /** @see .shopee-docs/Push Mechanism/Product Push/violation_item_push.md */
    case ViolationItem = 16;

    /** @see .shopee-docs/Push Mechanism/Consignment Service Push/supplier_create_product_push.md */
    case SupplierCreateProduct = 18;

    /** @see .shopee-docs/Push Mechanism/Consignment Service Push/supplier_prouduct_review_result_push.md */
    case SupplierProductReviewResult = 19;

    /** @see .shopee-docs/Push Mechanism/Consignment Service Push/purchase_order_Push.md */
    case PurchaseOrder = 20;

    /** @see .shopee-docs/Push Mechanism/Consignment Service Push/inbound_status_push.md */
    case InboundStatus = 21;

    /** @see .shopee-docs/Push Mechanism/Product Push/item_price_update_push.md */
    case ItemPriceUpdate = 22;

    /** @see .shopee-docs/Push Mechanism/Order Push/booking_status_push.md */
    case BookingStatus = 23;

    /** @see .shopee-docs/Push Mechanism/Order Push/booking_trackingno_push.md */
    case BookingTrackingNumber = 24;

    /** @see .shopee-docs/Push Mechanism/Order Push/booking_shipping_document_status_push.md */
    case BookingShippingDocumentStatus = 25;

    /** @see .shopee-docs/Push Mechanism/Product Push/item_scheduled_publish_failed_push.md */
    case ItemScheduledPublishFailed = 27;

    /** @see .shopee-docs/Push Mechanism/Shopee Push/shop_penalty_update_push.md */
    case ShopPenaltyUpdate = 28;

    /** @see .shopee-docs/Push Mechanism/Return Push/return_updates_push.md */
    case ReturnUpdates = 29;

    /** @see .shopee-docs/Push Mechanism/Order Push/package_fulfillment_status_push.md */
    case PackageFulfillmentStatus = 30;

    /** @see .shopee-docs/Push Mechanism/Fulfillment by Shopee Push/fbs_br_invoice_issued_push.md */
    case FbsBrInvoiceIssued = 31;

    /** @see .shopee-docs/Push Mechanism/Fulfillment by Shopee Push/fbs_br_invoice_error_push.md */
    case FbsBrInvoiceError = 33;

    /** @see .shopee-docs/Push Mechanism/Fulfillment by Shopee Push/fbs_br_block_shop_push.md */
    case FbsBrBlockShop = 34;

    /** @see .shopee-docs/Push Mechanism/Fulfillment by Shopee Push/fbs_br_block_sku_push.md */
    case FbsBrBlockSku = 35;

    /** @see .shopee-docs/Push Mechanism/Fulfillment by Shopee Push/fbs_sellable_stock.md */
    case FbsSellableStock = 36;

    /** @see .shopee-docs/Push Mechanism/Order Push/courier_delivery_binding_status_push.md */
    case CourierDeliveryBindingStatus = 37;

    /** @see .shopee-docs/Push Mechanism/Shopee Push/video_upload_result_push.md */
    case VideoUploadResult = 38;

    /** @see .shopee-docs/Push Mechanism/Order Push/package_info_push.md */
    case PackageInfo = 47;
}
