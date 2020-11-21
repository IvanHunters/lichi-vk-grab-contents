<?php


namespace Lichi\Grab\Post;


class Post implements \Lichi\Grab\Post
{
    public int $postId;
    public int $ownerId;
    public int $unixTime;
    public int $likes;
    public int $views;
    public string $textPost;
    public array $images = [];
    public array $videos = [];
    public array $audios = [];

    public function __construct(array $postInfo){
        $this->unixTime = $postInfo['date'];
        $this->textPost = $postInfo['text'];
        $this->ownerId = $postInfo['from_id'];
        $this->postId = $postInfo['id'];
        $this->likes = $postInfo['likes']['count'];
        $this->views = $postInfo['views']['count'];
        if (isset($postInfo['attachments'])) {
            $this->parseAttachments($postInfo['attachments']);
        }
    }

    private function parseAttachments(array $attachments){
        foreach ($attachments as $attachment) {
            switch ($attachment['type']) {
                case 'photo':
                    $photos = $this->parsePhoto($attachment['photo']);
                    $this->images['maxSizeImageUrl'][] = $photos['maxSizeImageUrl'];
                    $this->images['mediumSizeImageUrl'][] = $photos['mediumSizeImageUrl'];
                    $this->images['smallSizeImageUrl'][] = $photos['smallSizeImageUrl'];
                    break;
                case 'audio':
                    $this->audios[] = $this->parseAudio($attachments['audio']);
                    break;
                case 'video':
                    $attachmentVideo = $this->parseVideo($attachments['video']);
                    if ($attachmentVideo != "")
                    {
                        $this->videos[] = $attachmentVideo;
                    }
                    break;
            }
        }
    }

    private function parsePhoto(array $photo): array
    {
        $return = [];

        $sizesForImage = $photo['sizes'];
        $maxSizesForImage = count($sizesForImage) - 1;
        $mediumSizesForImage = (int) ($maxSizesForImage / 2);
        $minSizesForImage = 0;

        $return['maxSizeImageUrl'] = $sizesForImage[$maxSizesForImage]['url'];
        $return['mediumSizeImageUrl'] = $sizesForImage[$mediumSizesForImage]['url'];
        $return['smallSizeImageUrl'] = $sizesForImage[$minSizesForImage]['url'];

        return $return;
    }

    private function parseAudio(array $audio): string
    {
        return "audio" . $audio['owner_id'] . "_" . $audio['id'];
    }

    private function parseVideo(array $video): string
    {
        if($video['can_add'] == 1)
        {
            return "video" . $video['owner_id'] . "_" . $video['id'];
        }else{
            return "";
        }
    }

}