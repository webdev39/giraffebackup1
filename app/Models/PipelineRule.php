<?php

namespace App\Models;

/**
 * App\Models\PipelineRule
 *
 * @property int $id
 * @property int $pipeline_id
 * @property int $pipeline_filter_id
 * @property string $name
 * @property string|null $description
 * @property mixed $keywords
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Board[] $boards
 * @property-read \App\Models\PipelineFilter $filter
 * @property-read \App\Models\Pipeline $pipeline
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PipelineRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PipelineRule whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PipelineRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PipelineRule whereKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PipelineRule whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PipelineRule wherePipelineFilterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PipelineRule wherePipelineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PipelineRule whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PipelineRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PipelineRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PipelineRule query()
 */
class PipelineRule extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pipeline_id',
        'pipeline_filter_id',
        'name',
        'description',
        'keywords'
    ];
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pipeline_rules';
    
    /**
     * Get data from json string
     *
     * @return mixed
     */
    public function getKeywordsAttribute()
    {
        return json_decode($this->attributes['keywords'], true);
    }
    
    /**
     * Save passed data as json string
     *
     * @param array $value
     *
     * @return void
     */
    public function setKeywordsAttribute(array $value)
    {
        $this->attributes['keywords'] = json_encode($value, JSON_FORCE_OBJECT);
    }
    
    /**
     * Get the associated pipeline.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class);
    }
    
    /**
     * Get the associated filter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function filter()
    {
        return $this->belongsTo(PipelineFilter::class, 'pipeline_filter_id', 'id', 'pipelineFilter');
    }
    
    /**
     * Get the associated boards.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function boards()
    {
        return $this->belongsToMany(Board::class, 'pipeline_rule_board');
    }
    
    public function getKeywords(): array
    {
        $filterBy = '';
        if (strpos($this->filter->name, '_')) {
            //
        } else {
            $filterBy = $this->filter->name;
        }
        $filterBy = strtoupper($filterBy);
        if($filterBy == 'BODY') {
            $filterBy = 'TEXT';
        }
        $keywords = [];
        foreach ($this->keywords as $keyword) {
            $keywords[] = [$filterBy, $keyword];
        }
        
        return  $keywords;
    }
}
