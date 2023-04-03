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
            'title'           => $this->faker->realText(50),
            'excerpt'         => $this->faker->realText(120),
            'slug'            => $this->faker->slug,
            'body'            => $this->faker->realText(1000),
            'article_type_id' => ArticleTypeEnum::random()->getId(),
            //dates between now and 30 days ago
            'created_at'      => $created_at = now()->subDays(random_int(1, 30)),
            'updated_at'      => $updated_at = $this->faker->dateTimeBetween($created_at, 'now'),
            'published_at'    => $this->faker->dateTimeBetween($updated_at, 'now'),
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

    public function sequential(): self
    {
        return $this->sequence(
            ...array_map(fn($articleType) => ['article_type_id' => $articleType->getId()], ArticleTypeEnum::all())
        );
    }

    public function configure(): self
    {
        return $this->afterCreating(function (Article $article) {
            $article->addMedia(UploadedFile::fake()->image('banner.jpg', 300, 200))
                ->toMediaCollection(
                    MediaCollectionEnum::ArticleBanners->name,
                    DiskEnum::public()
                );
        });
    }

}
