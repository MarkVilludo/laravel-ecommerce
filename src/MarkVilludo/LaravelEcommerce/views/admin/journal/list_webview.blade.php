<h4> {{$journal->category->name}} </h4>
<span>{{$journal->title}}</span>
<p>{{$journal->created_at->format('F d, Y')}}</p>
<div>{!!$journal->content!!} </div>