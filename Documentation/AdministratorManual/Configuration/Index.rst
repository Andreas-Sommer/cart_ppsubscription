.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

Main Configuration
==================

::

    plugin.tx_paypalsubscription {
		settings {
			sandbox =
			client =
			secret =
			subscription {
				pid =
			}
		}
	}

settings.sandbox
"""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_paypalsubscription.settings.sandbox
   Data type
      boolean
   Description
      PayPal Api in sandbox mod or live mode

settings.client
"""""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_paypalsubscription.settings.client
   Data type
      string
   Description
      PayPal API Client

settings.secret
"""""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_paypalsubscription.settings.secret
   Data type
      string
   Description
      PayPal API Secret


settings.subscription.pid
"""""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_paypalsubscription.settings.subscription.pid
   Data type
      int
   Description
      Page ID for return url after sign in PayPal and complete the authorization
