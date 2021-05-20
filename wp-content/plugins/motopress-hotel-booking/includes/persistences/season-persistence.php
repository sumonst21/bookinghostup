<?php

namespace MPHB\Persistences;

class SeasonPersistence extends CPTPersistence {

	/**
	 *
	 * @global \WPDB $wpdb
	 * @param array $atts
	 * @param \DateTime $atts['from_date']			Optional. Default is today.
	 * @param \DateTime $atts['to_date']			Optional. Default is null.
	 * @param int		$atts['count']				Optional. Count of seasons to search.
	 * @return int[] Array of season IDs.
	 */
	public function searchSeasons( $atts = array() ){
		global $wpdb;

		$defaultAtts = array(
			'from_date'	 => new \DateTime( current_time( 'mysql' ) ),
			'to_date'	 => null,
			'count'		 => null
		);

		$atts = array_merge( $defaultAtts, $atts );

		$query = "SELECT seasons.ID as ID FROM $wpdb->posts AS seasons";
		$where = " WHERE seasons.post_type = '" . MPHB()->postTypes()->season()->getPostType() . "'";
		$order = '';

		if ( !empty( $atts['from_date'] ) || !empty( $atts['to_date'] ) ) {
			// If one of the dates is empty, then from_date = to_date
			if ( empty( $atts['to_date'] ) ) {
				$atts['to_date'] = clone $atts['from_date'];
			} else if ( empty( $atts['from_date'] ) ) {
				$atts['from_date'] = clone $atts['to_date'];
			}

			$join = " INNER JOIN $wpdb->postmeta AS seasonStartDate
						ON ( seasons.ID = seasonStartDate.post_id )
					  INNER JOIN $wpdb->postmeta AS seasonEndDate
						ON ( seasons.ID = seasonEndDate.post_id )";
			$whereSeason = " AND seasonStartDate.meta_key = 'mphb_start_date'
							  AND seasonEndDate.meta_key = 'mphb_end_date'
							  AND CAST(seasonStartDate.meta_value AS DATE) <= '%s'
							  AND CAST(seasonEndDate.meta_value AS DATE) >= '%s'";

			$query .= $join;
			$where .= $wpdb->prepare( $whereSeason, $atts['from_date']->format( 'Y-m-d' ), $atts['to_date']->format( 'Y-m-d' ) );
		}

		if ( !is_null( $atts['count'] ) ) {
			$order .= $wpdb->prepare( ' LIMIT %d', $atts['count'] );
		}

		$query .= $where . $order;

		$seasons = $wpdb->get_col( $query );

		// Convert IDs to integers
		array_walk( $seasons, function( &$value, $index ) {
			$value = (int)$value;
		} );

		return $seasons;
	}

	/**
	 *
	 * @param array $atts
	 * @param \DateTime $atts['from_date']			Optional. Default is today.
	 * @param \DateTime $atts['to_date']			Optional. Default is null.
	 * @param int		$atts['count']				Optional. Count of seasons to search.
	 * @return int First found season ID.
	 */
	public function searchSeason( $atts = array() ){
		$seasonIds = $this->searchSeasons( $atts );

		if ( empty( $seasonIds ) ) {
			return 0;
		}

		// Filter seasons by starting day number
		if ( !is_null( $atts['from_date'] ) ) {

			$dayNumber = (int)$atts['from_date']->format('w');

			foreach ( $seasonIds as $seasonId ) {
				$season = MPHB()->getSeasonRepository()->findById( $seasonId );

				if ( !$season ) {
					continue;
				}

				if ( in_array( $dayNumber, $season->getDays() ) ) {
					return $seasonId;
				}
			}

			// Nothing found
			return 0;

		} else {
			return reset( $seasons );
		}
	}

}
