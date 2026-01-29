Feature: Movie catalog

    Scenario: Create movie
        When I create a movie "Inception" with length 148 minutes
        Then The movie should be created successfully
        And The movie should have title "Inception"
        And The movie should have length 148 minutes
        And The movie should have status "upcoming"
        And Integration event "MovieCreated" should be published

    Scenario: Get movie
        Given There is a movie "movie1"
        When I retrieve the movie "movie1"
        Then The movie should be retrieved successfully

    Scenario: Get movie list
        Given There is a movie "movie1"
        And There is a movie "movie2"
        When I retrieve a movie list
        Then The movie list total should be 2
        And The movie list should contain 2 items

    Scenario: Update movie details
        Given There is a movie "movie1" with title "Old Title"
        When I update the movie "movie1" details with title "New Title"
        Then The movie "movie1" should exists
        And The movie should have title "New Title"
        And Integration event "MovieDetailsUpdated" should be published

    Scenario: Update movie length
        Given There is a movie "movie1" with length 100 minutes
        When I update the movie "movie1" length to 110 minutes
        Then The movie "movie1" should exists
        And The movie should have length 110 minutes
        And Integration event "MovieLengthUpdated" should be published

    Scenario: Release movie
        Given There is a movie "movie1" with status "upcoming"
        When I release the movie "movie1"
        Then The movie "movie1" should exists
        And The movie should have status "released"
        And Integration event "MovieReleased" should be published

    Scenario: Archive movie
        Given There is a movie "movie1" with status "upcoming"
        When I archive the movie "movie1"
        Then The movie "movie1" should exists
        And The movie should have status "archived"
        And Integration event "MovieArchived" should be published

    Scenario: Cannot get non-existing movie
        When I retrieve the movie "non_existing_movie"
        Then There should be a movie not found error

    Scenario: Cannot update non-existent movie details
        When I update the movie "movie1" details with title "New Title"
        Then There should be a movie not found error

    Scenario: Cannot update length of non-existing movie
        When I update the movie "non_existing_movie" length to 120 minutes
        Then There should be a movie not found error

    Scenario: Cannot release archived movie
        Given There is a movie "movie1" with status "archived"
        When I release the movie "movie1"
        Then There should be an invalid movie status error

    Scenario: Cannot update details of archived movie
        Given There is a movie "movie1" with status "archived" and title "Old Title"
        When I update the movie "movie1" details with title "New Title"
        Then There should be an invalid movie status error
        And The movie "movie1" should exists
        And The movie should have title "Old Title"

    Scenario: Cannot update length of archived movie
        Given There is a movie "movie1" with status "archived" and length 100 minutes
        When I update the movie "movie1" length to 110 minutes
        Then There should be an invalid movie status error
        And The movie "movie1" should exists
        And The movie should have length 100 minutes

    Scenario: Cannot release movie again
        Given There is a movie "movie1" with status "released"
        When I release the movie "movie1"
        Then There should be an invalid movie status error

    Scenario: Cannot archive movie again
        Given There is a movie "movie1" with status "archived"
        When I archive the movie "movie1"
        Then There should be an invalid movie status error
