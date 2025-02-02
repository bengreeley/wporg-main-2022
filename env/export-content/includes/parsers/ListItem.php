<?php

namespace WordPress_org\Main_2022\ExportToPatterns\Parsers;

class ListItem implements BlockParser {
	use GetSetAttribute;

	public function to_strings( array $block ) : array {
		$strings = $this->get_attribute( 'placeholder', $block );

		$matches = [];

		if ( preg_match( '/<li[^>]*>(.+)<\/li>/is', $block['innerHTML'], $matches ) ) {
			if ( ! empty( $matches[1] ) ) {
				$strings[] = $matches[1];
			}
		}

		return $strings;
	}

	// todo: this needs a fix to properly rebuild innerContent - similar to ParagraphParserTest
	public function replace_strings( array $block, array $replacements ) : array {
		$this->set_attribute( 'placeholder', $block, $replacements );

		$html = $block['innerHTML'];

		foreach ( $this->to_strings( $block ) as $original ) {
			if ( ! empty( $original ) && isset( $replacements[ $original ] ) ) {
				$regex = '#(<li[^>]*>)(' . preg_quote( $original, '/' ) . ')(<\/li>)#is';
				$html  = preg_replace( $regex, '${1}' . addcslashes( $replacements[ $original ], '\\$' ) . '${3}', $html );
			}
		}

		$block['innerHTML']    = $html;
		$block['innerContent'] = [ $html ];

		return $block;
	}
}
