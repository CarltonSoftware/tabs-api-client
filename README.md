tabs-api-client
===============

[![Build Status](http://api-dev.nocc.co.uk:8080/buildStatus/icon?job=tabs-api-client)](http://api-dev.nocc.co.uk:8080/job/tabs-api-client/)

Namespaced version of the tocc api client


Change Log:

5/8/2013, v.1 - Initial Release

7/8/2013, v.1.1 - Imported examples from old client

8/8/2013, v.1.3 - Added fetchAll function to property search.  ApiClient has the facility to perform concurrent connections.

9/8/2013, v.1.4 - Payment/Booking fixes

13/8/2013, v1.5 - Property searching for all properties now requests data concurrently.

20/8/2013, v1.51 - Added set accessors to Enquiry/Booking objects so that pricing properties can be updated on the fly. For example calling $booking->setSecurityDeposit(0) will set the amount of the SD to zero.  Added test to confirm this.

03/09/2013, v1.52 - Added getNumberOfNights accessor to pricing object
