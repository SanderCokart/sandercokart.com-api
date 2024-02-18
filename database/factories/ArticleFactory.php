<?php

namespace Database\Factories;

use App\Enums\ArticleTypeEnum;
use App\Enums\DiskEnum;
use App\Enums\MediaCollectionEnum;
use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->realText(50),
            'description' => $this->faker->realText(120),
            'slug' => $this->faker->slug,
            'body' => $this->faker->realText(1000),
            'article_type_id' => ArticleTypeEnum::random()->getId(),
            //dates between now and 30 days ago
            'created_at' => $created_at = now()->subDays(random_int(1, 30)),
            'updated_at' => $updated_at = $this->faker->dateTimeBetween($created_at, 'now'),
            'published_at' => $this->faker->dateTimeBetween($updated_at, 'now'),
        ];
    }

    public function published(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'published_at' => $this->faker->dateTimeBetween($attributes['updated_at'], 'now'),
            ];
        });
    }

    public function draft(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'published_at' => null,
            ];
        });
    }

    public function type(ArticleTypeEnum $articleType): self
    {
        return $this->state(function (array $attributes) use ($articleType) {
            return [
                'article_type_id' => $articleType->getId(),
            ];
        });
    }

    public function roundRobinArticleTypes(): self
    {
        return $this->sequence(
            ...array_map(fn(ArticleTypeEnum $articleType) => ['article_type_id' => $articleType->getId()], ArticleTypeEnum::all())
        );
    }

    public function configure(): self
    {
        return $this->afterCreating(function (Article $article) {
            $file = $this->createImageUploadedFile(300, 200, $article->title);
            $article->addMedia($file)
                ->toMediaCollection(
                    MediaCollectionEnum::ArticleBanners(),
                    DiskEnum::public()
                );
        });
    }

    private function createImageUploadedFile(string $width, string $height, string $title): UploadedFile
    {
        $colors = collect(['FF0000', '00FF00', '0000FF', 'FFFF00', '00FFFF', 'FF00FF']);
        $color = $colors->random();

        $image = imagecreate($width, $height);
        $red = hexdec(substr($color, 0, 2));
        $green = hexdec(substr($color, 2, 2));
        $blue = hexdec(substr($color, 4, 2));
        $background = imagecolorallocate($image, $red, $green, $blue);
        $textcolor = imagecolorallocate($image, 0,0,0);


        //text in the middle
        imagestring($image, 16, 50, 50, "#{$color}", $textcolor);
        imagestring($image, 16, 50, 75, $title, $textcolor);


        imagejpeg($image, $filename = tempnam(sys_get_temp_dir(), 'article_banner_'), 100);
        imagedestroy($image);
        return new UploadedFile($filename, 'article_banner.jpg', 'image/jpeg', null, true);
    }

}
