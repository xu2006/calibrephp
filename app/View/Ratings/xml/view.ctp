<?php
	$key = $rating['Rating']['rating'];
	$feed = $this->Opds->getDefaultXmlArray(array(
		'title'   => $this->Txt->stars($rating['Rating']['rating']/2),
		'id'      => array('calibre:rating:' . $rating['Rating']['id']),
		'updated' => $info['ratings']['count'][$key]['updated'],
	));

	$feed = $this->Opds->addLink($feed, array(
		'href'  => $this->Html->url(array('controller'=>'books', 'action'=>'opds'), false),
		'rel'   => 'start',
		'title' => 'Home',
	));

	$feed = $this->Opds->addLink($feed, array(
		'href' => $this->Html->url(array('controller'=>'ratings', 'action'=>'feed', $rating['Rating']['id'] . '.xml'), false),
		'rel'  => 'self',
	));

	foreach ($rating['Book'] as $book) {
		$entry = array(
			'link'      => $this->Html->url(array('controller'=>'ratings', 'action'=>'view', $rating['Rating']['id']. '.xml'), false),
			'title'     => $book['sort'],
			'updated'   => date(DATE_ATOM, strtotime($book['last_modified'])),
			'id'        => 'urn:uuid:' . $book['uuid'],
			'content'   => '',
			'author'    => $book['Author'],
			'published' => date(DATE_ATOM, strtotime($book['pubdate'])),
			'download'  => array('downloads' => $book['Datum'], 'bookpath' => $book['path']),
			'tag'       => $book['Tag'],
		);

		if (!empty($book['Series'])) {
			$entry['content'] = 'Book ' . $book['series_index'] . ' in the ' . $book['Series'][0]['sort'] . ' series';
		}
		if ($book['has_cover']) {
			$entry['thumbnail'] = $this->Image->resizeUrl($book['path'], $this->Image->resizeSettings['view']);
			$entry['image']     = $this->Image->resizeUrl($book['path'], $this->Image->resizeSettings['opds']);
		}
		$feed = $this->Opds->addEntry($feed, $entry);
	}

	$xmlObject = Xml::fromArray($feed);
	$feed      = $xmlObject->asXML();
	echo $feed;
?>