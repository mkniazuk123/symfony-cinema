Feature: Halls management

    Scenario: Create hall
        When I create a hall named "Number 1" with the following layout:
        """json
        {
            "rows": [
                {
                    "number": 1,
                    "segments": [
                        {
                            "type": "seatGroup",
                            "seats": [1, 2, 3]
                        },
                        {
                            "type": "gap"
                        },
                        {
                            "type": "seatGroup",
                            "seats": [4, 5, 6]
                        }
                    ]
                },
                {
                    "number": 2,
                    "segments": [
                        {
                            "type": "seatGroup",
                            "seats": [1, 2, 3, 4]
                        }
                    ]
                }
            ]
        }
        """
        Then The hall should be created successfully
        And The hall should be named "Number 1"
        And The hall should have capacity of 10 seats
        And The hall should be active

    Scenario: Rename hall
        Given There is a hall "hall1" named "Old Name"
        When I rename the hall "hall1" to "New Name"
        Then The hall "hall1" should exists
        And The hall should be named "New Name"

    Scenario: Update hall layout
        Given There is an active hall "hall1" with capacity of 3 seats and the following layout:
        """json
        {
            "rows": [
                {
                    "number": 1,
                    "segments": [
                        {
                            "type": "seatGroup",
                            "seats": [1, 2, 3]
                        }
                    ]
                }
            ]
        }
        """
        When I update the hall "hall1" layout to:
        """json
        {
            "rows": [
                {
                    "number": 1,
                    "segments": [
                        {
                            "type": "seatGroup",
                            "seats": [1, 2]
                        },
                        {
                            "type": "gap"
                        },
                        {
                            "type": "seatGroup",
                            "seats": [3, 4]
                        }
                    ]
                }
            ]
        }
        """
        Then The hall "hall1" should exists
        And The hall should have capacity of 4 seats

    Scenario: Archive hall
        Given There is an active hall "hall1"
        When I archive the hall "hall1"
        Then The hall "hall1" should exists
        And The hall should be archived

    Scenario: Cannot rename non-existing hall
        When I rename the hall "non_existing_hall" to "New Name"
        Then There should be a hall not found error

    Scenario: Cannot update layout of non-existing hall
        When I update the hall "non_existing_hall" layout
        Then There should be a hall not found error

    Scenario: Cannot archive non-existing hall
        When I archive the hall "non_existing_hall"
        Then There should be a hall not found error

    Scenario: Cannot create hall with invalid layout:
        When I create a hall with the following layout:
        """json
        {
            "rows": [
                {
                    "number": 1,
                    "segments": [
                        {
                            "type": "seatGroup",
                            "seats": [1, 2]
                        },
                        {
                            "type": "gap"
                        },
                        {
                            "type": "seatGroup",
                            "seats": [2, 3]
                        }
                    ]
                }
            ]
        }
        """
        Then There should be an invalid layout error with message "Duplicate seat number 2 in row 1"

    Scenario: Cannot rename an archived hall
        Given There is an archived hall "hall1" named "Old Name"
        When I rename the hall "hall1" to "New Name"
        Then There should be an invalid hall status error
        And The hall "hall1" should exists
        And The hall should be named "Old Name"

    Scenario: Cannot update layout of archived hall
        Given There is an archived hall "hall1" with capacity of 3 seats
        When I update the hall "hall1" layout to 5 seats
        Then There should be an invalid hall status error
        And The hall "hall1" should exists
        And The hall should have capacity of 3 seats

    Scenario: Cannot archive an already archived hall
        Given There is an archived hall "hall1"
        When I archive the hall "hall1"
        Then There should be an invalid hall status error
